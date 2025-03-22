import pandas as pd
import requests
from pathlib import Path

# Cargar el CSV con las URLs de imágenes
df = pd.read_csv("imagenes_facebook.csv")

# Crear carpeta para guardar las imágenes
output_dir = Path("imagenes_descargadas")
output_dir.mkdir(exist_ok=True)

# Descargar cada imagen
for i, url in enumerate(df["Image URL"]):
    try:
        response = requests.get(url, stream=True)
        if response.status_code == 200:
            image_path = output_dir / f"imagen_{i+1}.jpg"
            with open(image_path, "wb") as f:
                for chunk in response.iter_content(chunk_size=8192):
                    f.write(chunk)
            print(f"✅ Descargada: {image_path}")
        else:
            print(f"❌ Error al descargar {url}: Status {response.status_code}")
    except Exception as e:
        print(f"❌ Excepción en {url}: {e}")
