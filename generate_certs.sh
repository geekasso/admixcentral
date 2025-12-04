#!/bin/bash

# Create CA
openssl genrsa -out ca.key 2048
openssl req -x509 -new -nodes -key ca.key -sha256 -days 3650 -out ca.crt -subj "/C=US/ST=State/L=City/O=Org/CN=internal-ca"

# Create Server Cert
openssl genrsa -out server.key 2048
openssl req -new -key server.key -out server.csr -subj "/C=US/ST=State/L=City/O=Org/CN=server-cert"
openssl x509 -req -in server.csr -CA ca.crt -CAkey ca.key -CAcreateserial -out server.crt -days 3650 -sha256

# Output contents for PHP to read (base64 encoded to avoid escaping issues)
echo "CA_KEY=\"$(base64 -w 0 ca.key)\"" > certs.env
echo "CA_CRT=\"$(base64 -w 0 ca.crt)\"" >> certs.env
echo "SERVER_KEY=\"$(base64 -w 0 server.key)\"" >> certs.env
echo "SERVER_CRT=\"$(base64 -w 0 server.crt)\"" >> certs.env
