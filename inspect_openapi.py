import json

try:
    with open('/home/baga/Code/admixcentral/openapi.json', 'r') as f:
        data = json.load(f)
    
    paths = list(data['paths'].keys())
    dhcp_relay_paths = [p for p in paths if 'dhcp_relay' in p]
    
    with open('/home/baga/Code/admixcentral/inspect_openapi_output.txt', 'w') as out:
        out.write(f"Total paths: {len(paths)}\n")
        out.write(f"DHCP Relay paths: {dhcp_relay_paths}\n")
        
        if dhcp_relay_paths:
            for path in dhcp_relay_paths:
                out.write(f"\nMethods for {path}:\n")
                out.write(str(list(data['paths'][path].keys())) + "\n")

except Exception as e:
    with open('/home/baga/Code/admixcentral/inspect_openapi_output.txt', 'w') as out:
        out.write(f"Error: {e}\n")
