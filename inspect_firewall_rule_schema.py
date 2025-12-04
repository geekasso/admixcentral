import json

try:
    with open('/home/baga/Code/admixcenter/openapi.json', 'r') as f:
        data = json.load(f)
        if 'components' in data and 'schemas' in data['components'] and 'FirewallRule' in data['components']['schemas']:
            print(json.dumps(data['components']['schemas']['FirewallRule'], indent=2))
        else:
            print("FirewallRule schema not found")
except Exception as e:
    print(f"Error: {e}")
