<?php

return [
    'success'   => 200,
    'validation_fail'   => 400, /**validation failed */
    'controller_fail'   => 400, /**請求資料有誤 */
    'model_not_found'   => 400, /**請求項目不存在 */
    'token_invalid'   => 401,   /** token錯誤 */
    /**Normal Http status code */
    // 'unsupported_media_type' => 415,/** (Unsupported Media Type) — 不支援的媒體類型 */
    // 'not_acceptable'   => 406, /** (Not Acceptable) — 無法接受 */
    // 'method_not_allowed'   => 405, /** Server 對此 URI (目標資源)，不支援該請求方法。.  */
    'not_found'   => 404, /** （請求項目找不到）The server has not found anything matching the Request-URI.  */
    'forbiden'   => 403,     /* (不可存取此資源-權限不夠,資源不可被存取）The server understood the request, but is refusing to fulfill it. */
    'unauthorized'   => 401,   /** （token 錯誤, 未驗證）The request requires user authentication.  */
    'bad_request'   => 400, /** （請求資料內容錯誤）The request could not be understood by the server due to malformed syntax.  */
];