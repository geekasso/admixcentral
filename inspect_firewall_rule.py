import json

try:
    with open('/home/baga/Code/admixcenter/openapi.json', 'r') as f:
        data = json.load(f)
        path = '/api/v2/firewall/rule'
        if path in data['paths']:
            print(json.dumps(data['paths'][path], indent=2))
        else:
            print(f"Path {path} not found")
except Exception as e:
    print(f"Error: {e}")
