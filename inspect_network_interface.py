import json

try:
    with open('/home/baga/Code/admixcentral/openapi.json', 'r') as f:
        data = json.load(f)
        if 'components' in data and 'schemas' in data['components'] and 'NetworkInterface' in data['components']['schemas']:
            print(json.dumps(data['components']['schemas']['NetworkInterface'], indent=2))
        else:
            print("NetworkInterface schema not found")
except Exception as e:
    print(f"Error: {e}")
