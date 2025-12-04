import json

try:
    with open('/home/baga/Code/admixcentral/openapi.json', 'r') as f:
        data = json.load(f)
        paths = data.get('paths', {}).keys()
        for path in sorted(paths):
            print(path)
except Exception as e:
    print(f"Error: {e}")
