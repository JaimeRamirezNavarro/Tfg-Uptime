import time
import os
import sys
import json
import requests
import psutil
import subprocess
import socket

# ==========================================
# CONFIGURACIÓN DEL AGENTE (Rellenar al instalar)
# ==========================================
# Detecta automáticamente la IP del servidor para evitar problemas con localhost en Docker
def get_server_host():
    # Intenta obtener el host desde variables de entorno (Docker)
    host = os.getenv('API_HOST', '')
    if host:
        return host

    # Si no hay variable, usa el hostname de la máquina
    try:
        # Esto funciona tanto en Docker como en nativo
        hostname = socket.gethostname()
        # Si el hostname no es localhost, lo usamos
        if hostname not in ['localhost', '127.0.0.1']:
            return hostname
    except:
        pass

    # Fallback: usa localhost solo si estamos en la misma máquina
    return '127.0.0.1'

API_HOST = get_server_host()
API_PORT = os.getenv('API_PORT', '8080')  # Puerto por defecto 8080 (Docker)
API_URL = f"http://{API_HOST}:{API_PORT}/api/metrics"
API_TOKEN = os.getenv('API_TOKEN', 'YJ6YOPh3tWKe886Wp4BzDPrfhhLA158s')
INTERVAL = 5  # Segundos entre envíos

def get_local_metrics():
    # 1. Obtener métricas básicas del propio sistema (ZimaBlade)
    cpu = psutil.cpu_percent(interval=1)
    ram = psutil.virtual_memory().percent
    disk = psutil.disk_usage('/').percent  # Linux/ZimaBlade usa '/'

    # 2. Obtener información detallada de servicios y contenedores para ZimaBlade
    services = []
    containers = []

    # Obtener servicios de systemd (ZimaBlade es Linux)
    try:
        cmd = "systemctl list-units --type=service --state=running --no-pager | head -n 15 | awk 'NR>1 {print $1}'"
        output = subprocess.check_output(cmd, shell=True).decode()
        services = [s.strip().replace('.service', '') for s in output.split('\n') if s.strip()]
    except Exception as e:
        print(f"[{time.strftime('%H:%M:%S')}] Warning: No se pudo obtener servicios: {e}")
        sys.stdout.flush()

    # Obtener contenedores Docker (ZimaBlade típicamente corre Docker)
    try:
        cmd = 'docker ps --format "{{.Names}} ({{.Status}})" 2>/dev/null || echo ""'
        output = subprocess.check_output(cmd, shell=True).decode()
        containers = [c.strip() for c in output.split('\n') if c.strip()]
    except Exception as e:
        print(f"[{time.strftime('%H:%M:%S')}] Warning: No se pudo obtener contenedores Docker: {e}")
        sys.stdout.flush()

    # 3. Información adicional del sistema
    try:
        # Obtener uptime del sistema
        uptime_seconds = int(time.time() - psutil.boot_time())
        uptime_str = f"{uptime_seconds // 86400}d {(uptime_seconds % 86400) // 3600}h"
    except:
        uptime_str = "unknown"

    # Obtener hostname
    hostname = socket.gethostname()

    details = json.dumps({
        'services': services,
        'containers': containers,
        'hostname': hostname,
        'uptime': uptime_str,
        'platform': 'zimablade'
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
    print("==========================================================")
    print("  🚀 UPTIME AGENTE AUTÓNOMO - ZimaBlade Edition")
    print("==========================================================")
    print(f"  📡 API DESTINO: {API_URL}")
    print(f"  🖥️  HOSTNAME: {socket.gethostname()}")
    print(f"  ⏱️  INTERVALO: {INTERVAL}s")
    print("==========================================================")
    
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