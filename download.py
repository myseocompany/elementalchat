import pandas as pd
import requests
from pathlib import Path

# Cargar el CSV
df = pd.read_csv("creativos_facebook.csv")

# Crear una carpeta para los videos descargados
output_dir = Path("videos_descargados")
output_dir.mkdir(exist_ok=True)

# Descargar cada video
for i, url in enumerate(df["Video URL"]):
    try:
        response = requests.get(url, stream=True)
        if response.status_code == 200:
            video_path = output_dir / f"video_{i+1}.mp4"
            with open(video_path, "wb") as f:
                for chunk in response.iter_content(chunk_size=8192):
                    f.write(chunk)
            print(f"✅ Descargado: {video_path}")
        else:
            print(f"❌ Error al descargar {url}: Status {response.status_code}")
    except Exception as e:
        print(f"❌ Excepción en {url}: {e}")
