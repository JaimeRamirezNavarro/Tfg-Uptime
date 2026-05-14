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
API_URL="${API_URL:-http://localhost:8080/api/metrics}"
API_TOKEN="${API_TOKEN:-YJ6YOPh3tWKe886Wp4BzDPrfhhLA158s}"

echo -e "${YELLOW}[1/4]${NC} Creando directorio del agente..."
mkdir -p "$AGENT_DIR"

echo -e "${YELLOW}[2/4]${NC} Escribiendo daemon en Bash..."
cat > "$AGENT_DIR/agent.sh" << 'EOF'
#!/bin/bash
API_URL="${API_URL:-http://localhost:8080/api/metrics}"
API_TOKEN="${API_TOKEN:-YJ6YOPh3tWKe886Wp4BzDPrfhhLA158s}"

echo "=========================================="
echo "  UPTIME Agent (Native Bash) Iniciado"
echo "  Endpoint: $API_URL"
echo "=========================================="

while true; do
  CPU=$(top -bn1 | grep "Cpu(s)" | sed "s/.*, *\([0-9.]*\)%* id.*/\1/" | awk '{print 100 - $1}')
  CPU=${CPU:-0}
  RAM=$(free | grep Mem | awk '{print $3/$2 * 100.0}')
  RAM=${RAM:-0}
  DISK=$(df / | grep / | head -n 1 | awk '{ print $5}' | sed 's/%//g')
  DISK=${DISK:-0}

  SERVICES=$(systemctl list-units --type=service --state=running --no-pager | head -n 12 | tail -n +2 | awk '{print "\"" $1 "\""}' | paste -sd "," -)
  SERVICES=${SERVICES:-""}

  if command -v docker &> /dev/null; then
    CONTAINERS=$(docker ps --format '"{{.Names}} ({{.Status}})"' | paste -sd "," -)
  else
    CONTAINERS=""
  fi
  CONTAINERS=${CONTAINERS:-""}

  PAYLOAD="{\"cpu_load\": $CPU, \"ram_usage\": $RAM, \"disk_free\": $DISK, \"details\": {\"services\": [$SERVICES], \"containers\": [$CONTAINERS]}}"
  
  echo "[$(date +'%H:%M:%S')] Payload enviado: $PAYLOAD"
  
  curl -s -X POST "$API_URL" \
    -H "Authorization: Bearer $API_TOKEN" \
    -H "Content-Type: application/json" \
    -d "$PAYLOAD" > /dev/null

  sleep 15
done
EOF

chmod +x "$AGENT_DIR/agent.sh"

echo -e "${YELLOW}[3/4]${NC} Configurando systemd..."
cat > /etc/systemd/system/uptime-agent.service << EOF
[Unit]
Description=Uptime Monitoring Agent for ZimaBlade
After=network.target docker.service
Wants=docker.service

[Service]
Type=simple
User=root
ExecStart=/bin/bash $AGENT_DIR/agent.sh
Restart=always
RestartSec=5
Environment="API_URL=$API_URL"
Environment="API_TOKEN=$API_TOKEN"

[Install]
WantedBy=multi-user.target
EOF

echo -e "${YELLOW}[4/4]${NC} Reiniciando servicio..."
systemctl daemon-reload
systemctl enable uptime-agent.service
systemctl restart uptime-agent.service

sleep 2
if systemctl is-active --quiet uptime-agent; then
    echo -e "${GREEN}✓ Agente instalado correctamente!${NC}"
    echo "Usa 'journalctl -u uptime-agent -f' para ver los datos en vivo."
else
    echo -e "${RED}✗ Error al iniciar el agente.${NC}"
fi
