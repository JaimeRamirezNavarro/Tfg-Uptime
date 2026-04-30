# UPTIME Monitoring Platform v2.0

Sistema de monitorización de infraestructura en tiempo real con dashboard moderno y agentes autónomos.

## Características

- **Monitorización en Tiempo Real**: CPU, RAM, Disco, Servicios y Contenedores Docker
- **Multi-Tipo de Check**: Agente autónomo, Ping, HTTP
- **Dashboard Moderno**: UI oscura minimalista con actualizaciones en vivo vía WebSockets
- **Auto-Despliegue**: Instalación automática del agente vía SSH
- **ZimaBlade Ready**: Optimizado para despliegue en ZimaBlade como servidor central

## Arquitectura

```
┌─────────────────┐      ┌──────────────────┐      ┌─────────────────┐
│   ZimaBlade     │─────▶│   Dashboard      │◀─────│   Servidores    │
│   (Agente)      │ HTTP │   Laravel 13     │ HTTP │   Remotos       │
│   CPU/RAM/Docker│ POST │   + Reverb WS    │ POST │   (Ping/HTTP)   │
└─────────────────┘      └──────────────────┘      └─────────────────┘
```

## Inicio Rápido

### 1. Dashboard (Mac/Servidor Central)

```bash
cd ~/Desktop/uptime-server

# Opción A: Docker (Recomendado)
docker-compose up -d

# Opción B: Nativo con Laravel
composer install
npm install
php artisan migrate
php artisan serve --port=8080
```

El dashboard estará disponible en: `http://localhost:8080`

### 2. Agente en ZimaBlade

```bash
# Copia el script de instalación a tu ZimaBlade
scp agent-install.sh root@zimablade:/tmp/

# Conecta por SSH y ejecuta
ssh root@zimablade
cd /tmp
chmod +x agent-install.sh

# Configura las variables de entorno
export API_URL="http://<IP-TAILSCALE-MAC>:8080/api/metrics"
export API_TOKEN="YJ6YOPh3tWKe886Wp4BzDPrfhhLA158s"

# Ejecuta el instalador
./agent-install.sh
```

### 3. Verificar Conexión

```bash
# En el dashboard
curl http://localhost:8080/api/health

# En ZimaBlade
systemctl status uptime-agent
journalctl -u uptime-agent -f
```

## Configuración

### Variables de Entorno (.env)

```env
# App
APP_NAME=UPTIME
APP_DEBUG=true
APP_URL=http://localhost:8080

# Database (SQLite para desarrollo)
DB_CONNECTION=sqlite

# Reverb (WebSockets)
REVERB_APP_ID=xxx
REVERB_APP_KEY=xxx
REVERB_APP_SECRET=xxx

# Agente
API_HOST=127.0.0.1
API_PORT=8080
API_TOKEN=YJ6YOPh3tWKe886Wp4BzDPrfhhLA158s
```

### Health Check

Endpoint público para monitoring externo:

```bash
GET /api/health

Response:
{
  "status": "healthy",
  "timestamp": "2026-04-29T10:00:00+00:00",
  "service": "uptime-monitor",
  "version": "2.0.0"
}
```

## API Endpoints

### POST /api/metrics

Envía métricas desde el agente.

**Headers:**
```
Authorization: Bearer <API_TOKEN>
Content-Type: application/json
```

**Body:**
```json
{
  "cpu_load": 45.2,
  "ram_usage": 62.1,
  "disk_free": 78.5,
  "details": "{\"services\":[\"nginx\",\"docker\"],\"containers\":[\"app (Up)\"]}"
}
```

**Response:**
```json
{
  "status": "success",
  "data": { "id": 123, "cpu_load": 45.2, ... }
}
```

## Estructura del Proyecto

```
uptime-server/
├── app/
│   ├── Http/Controllers/Api/
│   │   └── MetricController.php    # API endpoint
│   ├── Livewire/
│   │   ├── Dashboard.php           # Vista principal
│   │   └── ServerDetail.php        # Detalles por servidor
│   └── Models/
│       ├── Server.php              # Modelo con Sanctum
│       └── Metric.php              # Métricas históricas
├── resources/
│   ├── css/app.css                 # Tailwind + Dark Theme
│   └── views/layouts/              # Blade templates
├── monitor.py                      # Agente Python
├── agent-install.sh                # Script instalación
├── docker-compose.yml              # Docker config
└── .env                            # Configuración
```

## Comandos Útiles

### Dashboard
```bash
# Docker
docker-compose logs -f
docker-compose restart

# Laravel
php artisan serve --port=8080
php artisan queue:work
php artisan pail  # Logs en tiempo real
```

### Agente (ZimaBlade)
```bash
systemctl status uptime-agent
journalctl -u uptime-agent -f
systemctl restart uptime-agent
systemctl stop uptime-agent
```

## Seguridad

- **Tokens API**: Cada servidor tiene un token único (32 chars)
- **SSH Passwords**: Encriptados en base de datos con Laravel Encrypter
- **Health Check**: Endpoint público sin autenticación

## Stack Tecnológico

| Componente | Tecnología |
|------------|------------|
| Backend | Laravel 13 + PHP 8.4 |
| Frontend | Livewire 4 + Alpine.js |
| WebSockets | Laravel Reverb |
| Base de Datos | SQLite (dev) / PostgreSQL (prod) |
| Agente | Python 3 + psutil |
| Container | Docker + docker-compose |

## Troubleshooting

### El agente no conecta
1. Verifica que el puerto 8080 esté accesible desde ZimaBlade
2. Si usas Tailscale: `export API_URL="http://<tailscale-ip>:8080/api/metrics"`
3. Revisa logs: `journalctl -u uptime-agent -f`

### Dashboard no muestra datos
1. Verifica health check: `curl http://localhost:8080/api/health`
2. Revisa logs Laravel: `storage/logs/laravel.log`
3. Reinicia Reverb: `php artisan reverb:start`

### Contenedores Docker no aparecen
- Asegúrate de que el usuario del agente tenga permisos Docker
- Prueba: `docker ps` manualmente en el servidor

## License

MIT License - Proyecto TFG

---

**Desarrollado para monitorización de infraestructura ZimaBlade**
