import json

try:
    with open('/home/baga/Code/admixcenter/openapi.json', 'r') as f:
        data = json.load(f)
        print("Root keys:", data.keys())
        if 'paths' in data:
            print("Number of paths:", len(data['paths']))
            # Print first 10 paths
            for i, path in enumerate(data['paths']):
                if i < 10:
                    print(path)
        else:
            print("'paths' key not found")
except Exception as e:
    print(f"Error: {e}")
