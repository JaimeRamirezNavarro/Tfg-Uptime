import time
import os
import sys
import json
import requests
import psutil
import subprocess

# ==========================================
# CONFIGURACIÓN DEL AGENTE (Rellenar al instalar)
# ==========================================
API_URL = "http://localhost:8000/api/metrics" # Tu dominio de Laravel
API_TOKEN = "TU_API_TOKEN_AQUI"               # El token generado en la BD para este servidor
INTERVAL = 30                                 # Segundos entre envíos

def get_local_metrics():
    # 1. Obtener métricas básicas del propio sistema
    cpu = psutil.cpu_percent(interval=1)
    ram = psutil.virtual_memory().percent
    disk = psutil.disk_usage('C:\\' if os.name == 'nt' else '/').percent
    
    # 2. (Opcional) Obtener servicios y contenedores
    services = []
    containers = []
    try:
        if os.name == 'nt':
            # Windows: Obtener primeros 10 servicios activos
            cmd = 'powershell -command "Get-Service | Where-Object {$_.Status -eq \'Running\'} | Select-Object -First 10 -ExpandProperty Name"'
            output = subprocess.check_output(cmd, shell=True).decode()
            services = [s.strip() for s in output.split('\r\n') if s.strip()]
        else:
            # Linux: Obtener servicios de systemd
            cmd = "systemctl list-units --type=service --state=running | head -n 10 | awk '{print $1}'"
            output = subprocess.check_output(cmd, shell=True).decode()
            services = [s.strip() for s in output.split('\n') if s.strip()]
    except Exception:
        pass

    try:
        # Docker
        cmd = 'docker ps --format "{{.Names}} ({{.Status}})"'
        output = subprocess.check_output(cmd, shell=True).decode()
        containers = [c.strip() for c in output.split('\n') if c.strip()]
    except Exception:
        pass

    details = json.dumps({
        'services': services,
        'containers': containers
    })

    return float(cpu), float(ram), float(disk), details

def send_to_api(cpu, ram, disk, details):
    headers = {
        "Authorization": f"Bearer {API_TOKEN}",
        "Accept": "application/json",
        "Content-Type": "application/json"
    }
    data = {
        "cpu_load": cpu,
        "ram_usage": ram,
        "disk_free": disk,
        "details": details
    }
    try:
        response = requests.post(API_URL, json=data, headers=headers, timeout=5)
        if response.status_code == 201:
            print(f"[{time.strftime('%H:%M:%S')}] OK: CPU {cpu}% | RAM {ram}% | Disco {disk}%")
        else:
            print(f"[{time.strftime('%H:%M:%S')}] Error API ({response.status_code}): {response.text}")
        sys.stdout.flush()
    except Exception as e:
        print(f"[{time.strftime('%H:%M:%S')}] Error de red conectando con API: {e}")
        sys.stdout.flush()

def run():
    print("==============================================")
    print("🚀 UPTIME AGENTE AUTÓNOMO INICIADO (Modo Push)")
    print(f"📡 API DESTINO: {API_URL}")
    print("==============================================")
    
    while True:
        try:
            cpu, ram, disk, details = get_local_metrics()
            send_to_api(cpu, ram, disk, details)
            time.sleep(INTERVAL)
        except KeyboardInterrupt:
            print("\n🛑 Agente detenido por el usuario.")
            break
        except Exception as e:
            print(f"Error inesperado: {e}")
            sys.stdout.flush()
            time.sleep(10)

if __name__ == "__main__":
    run()