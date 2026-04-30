#!/bin/bash
# ==========================================
# UPTIME AGENT INSTALLER - ZimaBlade Edition
# ==========================================
# Este script instala y configura el agente de monitorización
# en tu ZimaBlade o cualquier servidor Linux

set -e

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}"
echo "=========================================="
echo "  UPTIME Agent Installer"
echo "  ZimaBlade Edition v2.0"
echo "=========================================="
echo -e "${NC}"

# Configuración
AGENT_DIR="/opt/uptime-agent"
API_URL="${API_URL:-http://localhost:8080/api/metrics}"  # Localhost por defecto para ZimaBlade
API_TOKEN="${API_TOKEN:-YJ6YOPh3tWKe886Wp4BzDPrfhhLA158s}"

echo -e "${YELLOW}[1/5]${NC} Creando directorio del agente..."
mkdir -p "$AGENT_DIR"

echo -e "${YELLOW}[2/5]${NC} Descargando dependencias..."
# Verificar si pip está instalado
if ! command -v pip3 &> /dev/null; then
    echo -e "${RED}Error: pip3 no está instalado${NC}"
    echo "Instala pip3 con: sudo apt install python3-pip"
    exit 1
fi

# Instalar dependencias Python
pip3 install requests psutil --quiet

echo -e "${YELLOW}[3/5]${NC} Creando script del agente..."
cat > "$AGENT_DIR/monitor.py" << 'PYTHON_SCRIPT'
#!/usr/bin/env python3
import time
import os
import sys
import json
import requests
import psutil
import subprocess
import socket

# Configuración desde variables de entorno o valores por defecto
API_HOST = os.getenv('API_HOST', '127.0.0.1')
API_PORT = os.getenv('API_PORT', '8080')
API_TOKEN = os.getenv('API_TOKEN', '')
API_URL = f"http://{API_HOST}:{API_PORT}/api/metrics"
INTERVAL = 5

def get_local_metrics():
    cpu = psutil.cpu_percent(interval=1)
    ram = psutil.virtual_memory().percent
    disk = psutil.disk_usage('/').percent

    services = []
    containers = []

    # Obtener servicios systemd
    try:
        cmd = "systemctl list-units --type=service --state=running --no-pager | head -n 15 | awk 'NR>1 {print $1}'"
        output = subprocess.check_output(cmd, shell=True).decode()
        services = [s.strip().replace('.service', '') for s in output.split('\n') if s.strip()]
    except:
        pass

    # Obtener contenedores Docker
    try:
        cmd = 'docker ps --format "{{.Names}} ({{.Status}})" 2>/dev/null || echo ""'
        output = subprocess.check_output(cmd, shell=True).decode()
        containers = [c.strip() for c in output.split('\n') if c.strip()]
    except:
        pass

    uptime_seconds = int(time.time() - psutil.boot_time())
    hostname = socket.gethostname()

    details = json.dumps({
        'services': services,
        'containers': containers,
        'hostname': hostname,
        'uptime': f"{uptime_seconds // 86400}d {(uptime_seconds % 86400) // 3600}h",
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
            print(f"[{time.strftime('%H:%M:%S')}] OK: CPU {cpu}% | RAM {ram}% | Disk {disk}%")
        else:
            print(f"[{time.strftime('%H:%M:%S')}] Error API ({response.status_code})")
        sys.stdout.flush()
    except Exception as e:
        print(f"[{time.strftime('%H:%M:%S')}] Error: {e}")
        sys.stdout.flush()

def run():
    print("=" * 50)
    print("  UPTIME Agent - ZimaBlade")
    print(f"  API: {API_URL}")
    print(f"  Host: {socket.gethostname()}")
    print("=" * 50)

    while True:
        try:
            cpu, ram, disk, details = get_local_metrics()
            send_to_api(cpu, ram, disk, details)
            time.sleep(INTERVAL)
        except KeyboardInterrupt:
            print("\nAgente detenido.")
            break
        except Exception as e:
            print(f"Error: {e}")
            time.sleep(10)

if __name__ == "__main__":
    run()
PYTHON_SCRIPT

chmod +x "$AGENT_DIR/monitor.py"

echo -e "${YELLOW}[4/5]${NC} Creando servicio systemd..."
cat > /etc/systemd/system/uptime-agent.service << EOF
[Unit]
Description=Uptime Monitoring Agent for ZimaBlade
After=network.target docker.service
Wants=docker.service

[Service]
Type=simple
User=root
ExecStart=/usr/bin/python3 $AGENT_DIR/monitor.py
Restart=always
RestartSec=5
Environment="API_HOST=$API_HOST"
Environment="API_PORT=$API_PORT"
Environment="API_TOKEN=$API_TOKEN"

[Install]
WantedBy=multi-user.target
EOF

echo -e "${YELLOW}[5/5]${NC} Iniciando servicio..."
systemctl daemon-reload
systemctl enable uptime-agent.service
systemctl restart uptime-agent.service

# Verificar estado
sleep 2
if systemctl is-active --quiet uptime-agent; then
    echo -e "${GREEN}✓ Agente instalado y ejecutándose correctamente${NC}"
    echo ""
    echo "Comandos útiles:"
    echo "  systemctl status uptime-agent  - Ver estado"
    echo "  journalctl -u uptime-agent -f  - Ver logs"
    echo "  systemctl stop uptime-agent    - Detener"
else
    echo -e "${RED}✗ El agente no pudo iniciarse${NC}"
    echo "Revisa los logs con: journalctl -u uptime-agent -f"
    exit 1
fi

echo ""
echo -e "${BLUE}Configuración:${NC}"
echo "  API URL: $API_URL"
echo "  Token: ${API_TOKEN:0:8}..."
echo ""
