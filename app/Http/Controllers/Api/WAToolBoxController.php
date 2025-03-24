<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Customer;
use App\Models\Message;

use Illuminate\Support\Facades\Auth;

use App\Services\MessageService;
use App\Models\MessageSource;
use App\Models\RequestLog;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\File as HttpFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Namu\WireChat\Enums\MessageType;
use Namu\WireChat\Events\MessageCreated;
use Namu\WireChat\Jobs\NotifyParticipants;
use Namu\WireChat\Models\Message as ModelsMessage;
use App\Enums\WAMessageType;


class WAToolBoxController extends Controller{
    public $imageBase64 = "";

    public function receiveMessage(Request $request){
        $this->saveRequestLog($request);
    
        Log::info('Receiving data at WAToolBoxController receiveMessage:', [$request->all()]);
        // Validar los datos del request
        $validatedData = $request->validate([
            'id' => 'required|string',
            'type' => 'required|string',
            'user' => 'required|string',
            'phone' => 'required|string',
            'content' => 'required|string',
            'name' => 'required|string',
            'name2' => 'string|nullable',
            'image' => 'string|nullable',
            'APIKEY' => 'required|string'
        ]);

        //Log::info('ValidatedData', [$validatedData]);
        
        // Identificar el Message Source
        $messageSource = MessageSource::where('APIKEY', $validatedData['APIKEY'])->first();
        if (!$messageSource) {
            Log::info('MessageSource. Error Fuente del mensaje no encontrada', [$validatedData['APIKEY']]);
            return response()->json(['message' => 'Fuente del mensaje no encontrada'], 404);
        }else{
            //Log::info('MessageSource. Fuente del mensaje encontrada');
        }
        $reciver_phone = $messageSource->settings['phone_number']; // 57300...

        
        // inicio de guardar imagen
        $sender = Customer::firstOrNew(['phone' => $validatedData['phone']]);
        $isNewCustomer = !$sender->exists;

        if ($isNewCustomer) {
            $sender->name = $validatedData['name'] ?? $validatedData['name2'];
        }

        if (!empty($validatedData['image'])) {
            $currentImage = $sender->image_url;
            $shouldUpdateImage = $this->shouldUpdateCustomerImage($currentImage, $validatedData['image']);
            
            $shouldUpdateImage = true;
            Log::info('shouldUpdateImage', [$shouldUpdateImage]);

            if ($shouldUpdateImage) {
                $savedUrl = $this->saveCustomerImage($validatedData['image'], $sender->id);
                if ($savedUrl) {
                    $sender->image_url = $savedUrl;
                }
            }
        }

        if ($isNewCustomer || $sender->isDirty()) {
            $sender->save();
        }

        // fin de guardar imagen
        

        logger(["image"=>$validatedData['image']]);

        logger(["customer"=>$sender]);

        

        //$receiver_user = User::findByPhone($reciver_phone);
        $receiver_user = User::find(2);

        if (!$receiver_user) {
            Log::warning('No se encontró un usuario con el teléfono: ' . $reciver_phone);
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }else{
            Log::info('Se asigno al usuario 2');
            
        }

        Log::info('Usuario identificado: ' . $receiver_user->name);

        Log::info('Telefono '.$reciver_phone);
        $conversation = $sender->createConversationWith($receiver_user);
        
        if($validatedData['type']=='chat'){
            $message = $sender->sendMessageTo($receiver_user, $validatedData['content']);
            

            //broadcast(new MessageCreated($message));
            //NotifyParticipants::dispatch($message->conversation,$message);

            //Get Participant from conversation
            $participant = $message->conversation->participant($receiver_user);

            Log::info('Telefono enviado '.$message);
            
            //Broadcast message to chat 
            broadcast(new MessageCreated($message));

            //Notify participant directly 
            broadcast(new \Namu\WireChat\Events\NotifyParticipant($participant, $message));
        }elseif ($validatedData['type']=='image') {
            try {
                $tmpFileObject= $this->validateBase64($validatedData['content'],['png,jpg,mp4,heic,HEIC']);
                $tmpFileObjectPathName = $tmpFileObject->getPathname();

                $file = new UploadedFile(
                    $tmpFileObjectPathName,
                    $tmpFileObject->getFilename(),
                    $tmpFileObject->getMimeType(),
                    0,
                    true
                );

                $path = $file->store(config('wirechat.attachments.storage_folder', 'attachments'), 
                        config('wirechat.attachments.storage_disk', 'public'));
                    
                logger('testing '.$conversation);
                $message = ModelsMessage::create([
                    'conversation_id' => $conversation->id,
                    'sendable_type' => $sender->getMorphClass(), // Polymorphic sender type
                    'sendable_id' => $sender->id, // Polymorphic sender ID
                    'type' => MessageType::ATTACHMENT,
                    // 'body' => $this->body, // Add body if required
                ]);
                $message->attachment()->create([
                    'file_path' => $path,
                    'file_name' => basename($path),
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'url' => Storage::url($path),
                ]);
                unlink($tmpFileObjectPathName); // delete temp file
             //   broadcast(new MessageCreated($message))->toOthers();
                //Get Participant from conversation
                $participant = $message->conversation->participant($receiver_user);

                Log::info('Telefono enviado '.$message);
                
                //Broadcast message to chat 
                broadcast(new MessageCreated($message));

                //Notify participant directly 
                broadcast(new \Namu\WireChat\Events\NotifyParticipant($participant, $message));

             //   NotifyParticipants::dispatch($message->conversation,$message);


            }
            catch (\Exception $e) {
                logger('error '.$e);
            } 
    } elseif ($validatedData['type'] == 'ptt') {
        try {
            // Validar y procesar el archivo base64 recibido
            $tmpFileObject = $this->validateBase64($validatedData['content'], ['mp3', 'wav', 'ogg']);
            if (!$tmpFileObject) {
                logger(['message' => 'Archivo de audio no válido']);
                return response()->json(['message' => 'Archivo de audio no válido'], 400);
            }
    
            // Obtener el path temporal
            $tmpFileObjectPathName = $tmpFileObject->getPathname();
    
            // Convertirlo en un UploadedFile para manejarlo con Laravel
            $file = new UploadedFile(
                $tmpFileObjectPathName,
                $tmpFileObject->getFilename(),
                $tmpFileObject->getMimeType(),
                0,
                true
            );
    
            // Guardar el archivo en el sistema de almacenamiento
            $path = $file->store(config('wirechat.attachments.storage_folder', 'attachments'), 
                    config('wirechat.attachments.storage_disk', 'public'));
    
            // Crear el mensaje en la conversación
            $message = ModelsMessage::create([
                'conversation_id' => $conversation->id,
                'sendable_type' => $sender->getMorphClass(),
                'sendable_id' => $sender->id,
                'type' => MessageType::ATTACHMENT, // Indica que es un archivo adjunto
            ]);
    
            // Asociar el archivo adjunto al mensaje
            $message->attachment()->create([
                'file_path' => $path,
                'file_name' => basename($path),
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'url' => Storage::url($path),
            ]);
    
            // Eliminar el archivo temporal
            unlink($tmpFileObjectPathName);
    
            // Notificar a los participantes
            $participant = $message->conversation->participant($receiver_user);
            broadcast(new MessageCreated($message))->toOthers();
            broadcast(new \Namu\WireChat\Events\NotifyParticipant($participant, $message));
            NotifyParticipants::dispatch($message->conversation, $message);

            $message = "Mensaje de audio procesado correctamente.";
    
            Log::info('Mensaje de audio procesado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al procesar el audio: ' . $e->getMessage());
            return response()->json(['message' => 'Error al procesar el audio'], 500);
        }
    }
        

    return response()->json([
        'message' => 'Data processed successfully',
        'message_source' => $messageSource,
        'message' => $message,
    ], 200);


    // Si el lead existe pero no tiene nombre, actualizarlo
    /*
    if (is_null($sender->name)) {
        $sender->name = $validatedData['name2'];
        $sender->save();
    }


    // Almacenar la imagen si está presente
    $imageUrl = null;
    if (!empty($validatedData['image'])) {
        try {
            // Decodificar la imagen Base64 y guardarla
            $imageData = base64_decode($validatedData['image']);
            $tempFile = tempnam(sys_get_temp_dir(), 'img_'); // Crear un archivo temporal
            file_put_contents($tempFile, $imageData);
            $messageService = new MessageService();
            // Usar el servicio MessageService para guardar la imagen
            $imageUrl = $messageService->saveImage(new \Illuminate\Http\UploadedFile(
                $tempFile,
                'image.png'
            ));

            Log::info('Imagen almacenada con éxito: ' . $imageUrl);
        } catch (\Exception $e) {
            Log::error('Error al guardar la imagen: ' . $e->getMessage());
        }
    }

    // Crear el mensaje asociado al Lead
    $type_id = $this->determineMessageType($validatedData['type']);
    $message = $lead->messages()->create([
        'lead_id' => $lead->id,
        'type_id' => $type_id,
        'content' => $validatedData['content'],
        'message_source_id' => $messageSource->id, // Asocia la fuente del mensaje
        'message_type_id' => 1,
        'user_id' => 1, // Ajusta según corresponda el usuario relacionado
        'is_outgoing' => false,
        'media_url' => $imageUrl,
    ]);

    Log::info('Mensaje creado:', [
        'team_id' => $teamId,
        'message_source_id' => $messageSource->id,
        'lead_id' => $lead->id,
        'message_id' => $message->id,
    ]);
    /*/
    // Emitir el evento MessageReceived
    //MessageReceived::dispatch($validatedData['content'], $validatedData['phone']);

}
private function validateBase64(string $base64data, array $allowedMimeTypes)
{
    // strip out data URI scheme information (see RFC 2397)
    if (str_contains($base64data, ';base64')) {
        list(, $base64data) = explode(';', $base64data);
        list(, $base64data) = explode(',', $base64data);
    }

    // strict mode filters for non-base64 alphabet characters
    if (base64_decode($base64data, true) === false) {
        return false;
    }

    // decoding and then re-encoding should not change the data
    if (base64_encode(base64_decode($base64data)) !== $base64data) {
        return false;
    }

    $fileBinaryData = base64_decode($base64data);

    // temporarily store the decoded data on the filesystem to be able to use it later on
    $tmpFileName = tempnam(sys_get_temp_dir(), 'medialibrary');
    file_put_contents($tmpFileName, $fileBinaryData);

    $tmpFileObject = new HttpFile($tmpFileName);

    // guard against invalid mime types
    $allowedMimeTypes = Arr::flatten($allowedMimeTypes);

    // if there are no allowed mime types, then any type should be ok
    if (empty($allowedMimeTypes)) {
        return $tmpFileObject;
    }

    // Check the mime types
    $validation = FacadesValidator::make(
        ['file' => $tmpFileObject],
        ['file' => 'mimes:' . implode(',', $allowedMimeTypes)]
    );

    if($validation->fails()) {
        return false;
    }

    return $tmpFileObject;
}








    private function determineMessageType($type)
    {
        // Asigna un tipo de acción según el tipo recibido en WAToolbox
        // Ejemplo simple: chat, ptt, image
        $type_id = "";
        switch ($type) {
            case "text":
                $type_id = 1;
                break;
            case "image":
                $type_id = 2;
                break;
            case "audio":
                $type_id = 3;
                break;
        }

        return $type_id;
    }

    public function saveRequestLog(Request $request)
    {
        $model = new RequestLog();

        // Verificar si la solicitud es JSON
        if ($request->isJson()) {
            $requestData = $request->json()->all();
        } else {
            // Si no es JSON, obtener todos los datos de la solicitud
            $requestData = $request->all();
        }

        // Guardar la solicitud como JSON
        //logger($requestData);
        if(isset($requestData["type"]) && ($requestData["type"]!= WAMessageType::IMAGE->value)){
            $model->request =  json_encode($requestData);
            $model->save();

        }    
    }

    private function saveCustomerImage(string $imageUrl, ?int $customerId = null): ?string
    {
        try {
            // Si es base64
            if (str_starts_with($imageUrl, 'data:image')) {
                $imageParts = explode(';base64,', $imageUrl);
                $imageTypeAux = explode('image/', $imageParts[0]);
                $imageType = $imageTypeAux[1];
                $imageBase64 = base64_decode($imageParts[1]);
            } else {
                // Si es una URL externa
                $imageBase64 = file_get_contents($imageUrl);
                $imageType = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
            }

            // Generar un nombre único para el archivo
            $filename = 'customer_' . ($customerId ?? uniqid()) . '_' . time() . '.' . $imageType;

            // Ruta de guardado
            $path = 'customers/' . $filename;

            // Guardar en el disco configurado (por defecto: public)
            Storage::disk('public')->put($path, $imageBase64);

            // Devolver URL pública
            return $path; // <-- retorna solo el path relativo

        } catch (\Exception $e) {
            Log::error('Error guardando imagen del cliente: ' . $e->getMessage());
            return null;
        }
    }



    private function shouldUpdateCustomerImage(?string $currentImage, ?string $newImageUrl): bool
    {
        if (empty($newImageUrl)) return false;
        if (!$currentImage) return true;

        try {
            $newHash = $this->extractHashFromWhatsAppUrl($newImageUrl);
            $currentHash = pathinfo($currentImage, PATHINFO_FILENAME); // Ej: customer_27811_1742774763

            // comparar hashes: si no coincide, actualizar
            return !str_contains($currentImage, $newHash);
        } catch (\Exception $e) {
            Log::warning("Error comparando imágenes por hash: " . $e->getMessage());
            return true;
        }
    }




    private function extractHashFromWhatsAppUrl($url): ?string
    {
        $parts = parse_url($url);
        if (!isset($parts['query'])) return null;

        parse_str($parts['query'], $queryParams);
        return $queryParams['oh'] ?? null;
    }

}
