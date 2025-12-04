import json

try:
    with open('/home/baga/Code/admixcentral/openapi.json', 'r') as f:
        data = json.load(f)
        paths = data.get('paths', {}).keys()
        diag_paths = [p for p in paths if '/diagnostics' in p]
        print("Diagnostics Paths:")
        for p in diag_paths:
            print(p)
except Exception as e:
    print(f"Error: {e}")
