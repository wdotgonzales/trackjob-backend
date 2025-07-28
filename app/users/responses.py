from rest_framework.response import Response

# CustomResponse formats API responses with data, message, and status_code.
class CustomResponse(Response):
    def __init__(self, data=None, message=None, status_code=None, **kwargs):
        payload = {
            "data": data,
            "message": message,
            "status_code": status_code or kwargs.get("status", 200),
        }
        super().__init__(payload, status=payload["status_code"], **kwargs)