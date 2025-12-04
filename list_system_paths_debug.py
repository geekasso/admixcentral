import json

try:
    with open('/home/baga/Code/admixcenter/openapi.json', 'r') as f:
        data = json.load(f)
        paths = data.get('paths', {})
        system_paths = [p for p in paths.keys() if p.startswith('/system')]
        for p in sorted(system_paths):
            print(p)
except Exception as e:
    print(f"Error: {e}")
