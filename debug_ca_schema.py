import json
try:
    with open('openapi.json', 'r') as f:
        data = json.load(f)
    
    path = '/api/v2/system/certificate_authority'
    if path in data['paths'] and 'post' in data['paths'][path]:
        print(json.dumps(data['paths'][path]['post'], indent=2))
    else:
        print("Path or method not found")
except Exception as e:
    print(e)
