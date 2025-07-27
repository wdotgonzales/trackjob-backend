import os
from decouple import config

print("Environment Variable TEST:", config('TEST'))