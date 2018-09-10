listen 443 http2;
ssl on;

ssl_session_timeout 5m;
ssl_ciphers AES128+EECDH:AES128+EDH:!aNULL;
ssl_protocols TLSv1 TLSv1.1 TLSv1.2;
ssl_prefer_server_ciphers on;