import json

try:
    with open('/home/baga/Code/admixcentral/openapi.json', 'r') as f:
        data = json.load(f)
    
    try:
        schema = data['components']['schemas']['DHCPRelay']
        with open('/home/baga/Code/admixcentral/inspect_component_output.txt', 'w') as out:
            out.write(json.dumps(schema, indent=2) + "\n")
    except KeyError:
        with open('/home/baga/Code/admixcentral/inspect_component_output.txt', 'w') as out:
            out.write("DHCPRelay schema not found\n")

except Exception as e:
    with open('/home/baga/Code/admixcentral/inspect_component_output.txt', 'w') as out:
        out.write(f"Error: {e}\n")
