#!/bin/bash
# Script para generar certificados SSL válidos para iOS

echo "Generando certificados SSL para yaddielk44..."

# 1. Generar clave privada CA
openssl genrsa -out ca-key.pem 2048

# 2. Generar certificado CA auto-firmado
openssl req -new -x509 -days 3650 -key ca-key.pem -out ca-certificate.pem \
    -subj "/C=US/ST=California/L=Mountain View/O=yaddielk44 Enterprises/OU=Security/CN=yaddielk44 Root CA"

# 3. Generar clave privada del servidor
openssl genrsa -out server-key.pem 2048

# 4. Generar CSR (Certificate Signing Request)
openssl req -new -key server-key.pem -out server-csr.pem \
    -subj "/C=US/ST=California/L=Mountain View/O=yaddielk44 Enterprises/OU=Server/CN=*.yaddielk44.com"

# 5. Firmar certificado del servidor con la CA
openssl x509 -req -days 3650 -in server-csr.pem -CA ca-certificate.pem \
    -CAkey ca-key.pem -CAcreateserial -out server-certificate.pem

# 6. Crear archivo .p12 para iOS (password: yaddielk44)
openssl pkcs12 -export -out yaddielk44.p12 -inkey server-key.pem \
    -in server-certificate.pem -certfile ca-certificate.pem -passout pass:yaddielk44

# 7. Convertir certificado CA a formato DER para iOS
openssl x509 -in ca-certificate.pem -outform der -out ca-certificate.der

echo "✅ Certificados generados:"
echo "   - ca-certificate.pem (Certificado CA)"
echo "   - server-certificate.pem (Certificado servidor)"
echo "   - server-key.pem (Clave privada)"
echo "   - yaddielk44.p12 (Paquete PKCS12 para iOS)"
echo "   - ca-certificate.der (Certificado CA en formato DER)"