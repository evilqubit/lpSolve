import random
import json
import sys

# sys.argv[1]
# section count

myValues = []

for x in range(1, 8):
  for y in range(1, 5 ):
    myValues.append({'x%dy%d' % (x,y) : random.randrange(0, 10)})

print ( json.dumps( myValues ) )