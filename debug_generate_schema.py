import json
try:
    with open('openapi.json', 'r') as f:
        data = json.load(f)
    
    paths = ['/api/v2/system/certificate_authority/generate', '/api/v2/system/certificate/generate']
    for path in paths:
        if path in data['paths'] and 'post' in data['paths'][path]:
            print(f"Schema for {path}:")
            print(json.dumps(data['paths'][path]['post'], indent=2))
        else:
            print(f"Path or method not found for {path}")
except Exception as e:
    print(e)
