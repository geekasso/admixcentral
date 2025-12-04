import json

try:
    with open('/home/baga/Code/admixcentral/openapi.json', 'r') as f:
        data = json.load(f)
    
    path = '/api/v2/services/dhcp_relay'
    
    with open('/home/baga/Code/admixcentral/inspect_schema_output.txt', 'w') as out:
        if path in data['paths']:
            methods = data['paths'][path]
            if 'get' in methods:
                out.write("GET Response Schema:\n")
                try:
                    schema = methods['get']['responses']['200']['content']['application/json']['schema']
                    out.write(json.dumps(schema, indent=2) + "\n")
                except KeyError:
                    out.write("No schema found for GET 200\n")
            
            if 'patch' in methods:
                out.write("\nPATCH Body Schema:\n")
                try:
                    schema = methods['patch']['requestBody']['content']['application/json']['schema']
                    out.write(json.dumps(schema, indent=2) + "\n")
                except KeyError:
                    out.write("No schema found for PATCH requestBody\n")
        else:
            out.write(f"Path {path} not found\n")

except Exception as e:
    with open('/home/baga/Code/admixcentral/inspect_schema_output.txt', 'w') as out:
        out.write(f"Error: {e}\n")
