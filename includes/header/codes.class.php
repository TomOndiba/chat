<?php

/*!
  Copyright 2013 The Impact Plus. All rights reserved.

  YOU ARE PERMITTED TO:
  * Transfer the Software and license to another party if the other party agrees to accept the terms and conditions of this License Agreement. The license holder is responsible for a transfer fee of $50.95 USD. The license must be at least 90 days old or not transferred within the last 90 days;
  * Modify source codes of the software and add new functionality that does not violate the terms of the current license;
  * Customize the Software's design and operation to suit the internal needs of your web site except to the extent not permitted under this Agreement;
  * Create, sell and distribute applications/modules/plugins which interface (not derivative works) with the operation of the Software provided the said applications/modules/plugins are original works or appropriate 3rd party license(s) except to the extent not permitted under this Agreement;
  * Create, sell and distribute by any means any templates and/or designs/skins which allow you or other users of the Software to customize the appearance of Impact Plus provided the said templates and or designs/skins are original works or appropriate 3rd party license(s) except to the extent not permitted under this Agreement.

  YOU ARE "NOT" PERMITTED TO:
  * Use the Software in violation of any US/India or international law or regulation.
  * Permit other individuals to use the Software except under the terms listed above;
  * Reverse-engineer and/or disassemble the Software for distribution or usage outside your domain if it is not an unlimited licence version;
  * Use the Software in such as way as to condone or encourage terrorism, promote or provide pirated Software, or any other form of illegal or damaging activity;
  * Distribute individual copies of proprietary files, libraries, or other programming material in the Software package.
  * Distribute or modify proprietary graphics, HTML, or CSS packaged with the Software for use in applications other than the Software;
  * Use the Software in more than one instance or location (URL, domain, sub-domain, etc.) without prior written consent from IMPACT PLUS;
  * Modify the software and/or create applications and modules which allow the Software to function in more than one instance or location (URL, domain, sub-domain, etc.) without prior written consent from IMPACT PLUS;
  * Copy the Software and install that single program for simultaneous use on multiple machines without prior written consent from IMPACT PLUS;
*/

class ipStatusCodes {
  private static $codes = array(
    "1xx" =>  array(
      "label" =>  "Informational",
      "short" =>  "This class of status code indicates a provisional response, consisting only of the Status-Line and optional headers, and is terminated by an empty line. There are no required headers for this class of status code. Since HTTP/1.0 did not define any 1xx status codes, servers MUST NOT send a 1xx response to an HTTP/1.0 client except under experimental conditions.",
      "long"  =>  "This class of status code indicates a provisional response, consisting only of the Status-Line and optional headers, and is terminated by an empty line. There are no required headers for this class of status code. Since HTTP/1.0 did not define any 1xx status codes, servers MUST NOT send a 1xx response to an HTTP/1.0 client except under experimental conditions.\nA client MUST be prepared to accept one or more 1xx status responses prior to a regular response, even if the client does not expect a 100 (Continue) status message. Unexpected 1xx status responses MAY be ignored by a user agent.\nProxies MUST forward 1xx responses, unless the connection between the proxy and its client has been closed, or unless the proxy itself requested the generation of the 1xx response. (For example, if a\nproxy adds a \"Expect: 100-continue\" field when it forwards a request, then it need not forward the corresponding 100 (Continue) response(s).)"
    ),
    "100" =>  array(
      "label" =>  "Continue",
      "short" =>  "The client SHOULD continue with its request. This interim response is used to inform the client that the initial part of the request has been received and has not yet been rejected by the server.",
      "long"  =>  "The client SHOULD continue with its request. This interim response is used to inform the client that the initial part of the request has been received and has not yet been rejected by the server. The client SHOULD continue by sending the remainder of the request or, if the request has already been completed, ignore this response. The server MUST send a final response after the request has been completed."
    ),
    "101" =>  array(
      "label" =>  "Switching Protocols",
      "short" =>  "The server understands and is willing to comply with the client's request, via the Upgrade message header field, for a change in the application protocol being used on this connection. The server will switch protocols to those defined by the response's Upgrade header field immediately after the empty line which terminates the 101 response.",
      "long"  =>  "The server understands and is willing to comply with the client's request, via the Upgrade message header field, for a change in the application protocol being used on this connection. The server will switch protocols to those defined by the response's Upgrade header field immediately after the empty line which terminates the 101 response.\nThe protocol SHOULD be switched only when it is advantageous to do so. For example, switching to a newer version of HTTP is advantageous over older versions, and switching to a real-time, synchronous protocol might be advantageous when delivering resources that use such features."
    ),
    "2xx" =>  array(
      "label" =>  "Successful",
      "short" =>  "This class of status code indicates that the client's request was successfully received, understood, and accepted.",
      "long"  =>  "This class of status code indicates that the client's request was successfully received, understood, and accepted."
    ),
    "200" =>  array(
      "label" =>  "OK",
      "short" =>  "The request has succeeded.",
      "long"  =>  "The request has succeeded. The information returned with the response is dependent on the method used in the request, for example:\nGET an entity corresponding to the requested resource is sent in the response;\nHEAD the entity-header fields corresponding to the requested resource are sent in the response without any message-body;\nPOST an entity describing or containing the result of the action;\nTRACE an entity containing the request message as received by the end server."
    ),
    "201" =>  array(
      "label" =>  "Created",
      "short" =>  "The request has been fulfilled and resulted in a new resource being created.",
      "long"  =>  "The request has been fulfilled and resulted in a new resource being created. The newly created resource can be referenced by the URI(s) returned in the entity of the response, with the most specific URI for the resource given by a Location header field. The response SHOULD include an entity containing a list of resource characteristics and location(s) from which the user or user agent can choose the one most appropriate. The entity format is specified by the media type given in the Content-Type header field. The origin server MUST create the resource before returning the 201 status code. If the action cannot be carried out immediately, the server SHOULD respond with 202 (Accepted) response instead.\nA 201 response MAY contain an ETag response header field indicating the current value of the entity tag for the requested variant just created."
    ),
    "202" =>  array(
      "label" =>  "Accepted",
      "short" =>  "The request has been accepted for processing, but the processing has not been completed.",
      "long"  =>  "The request has been accepted for processing, but the processing has not been completed. The request might or might not eventually be acted upon, as it might be disallowed when processing actually takes place. There is no facility for re-sending a status code from an asynchronous operation such as this.\nThe 202 response is intentionally non-committal. Its purpose is to allow a server to accept a request for some other process (perhaps a batch-oriented process that is only run once per day) without requiring that the user agent's connection to the server persist until the process is completed. The entity returned with this response SHOULD include an indication of the request's current status and either a pointer to a status monitor or some estimate of when the user can expect the request to be fulfilled."
    ),
    "203" =>  array(
      "label" =>  "Non-Authoritative Information",
      "short" =>  "The returned metainformation in the entity-header is not the definitive set as available from the origin server.",
      "long"  =>  "The returned metainformation in the entity-header is not the definitive set as available from the origin server, but is gathered from a local or a third-party copy. The set presented MAY be a subset or superset of the original version. For example, including local annotation information about the resource might result in a superset of the metainformation known by the origin server. Use of this response code is not required and is only appropriate when the response would otherwise be 200 (OK)."
    ),
    "204" =>  array(
      "label" =>  "No Content",
      "short" =>  "The server has fulfilled the request but does not need to return an entity-body, and might want to return updated metainformation.",
      "long"  =>  "The server has fulfilled the request but does not need to return an entity-body, and might want to return updated metainformation. The response MAY include new or updated metainformation in the form of entity-headers, which if present SHOULD be associated with the requested variant.\nIf the client is a user agent, it SHOULD NOT change its document view from that which caused the request to be sent. This response is primarily intended to allow input for actions to take place without causing a change to the user agent's active document view, although any new or updated metainformation SHOULD be applied to the document currently in the user agent's active view.\nThe 204 response MUST NOT include a message-body, and thus is always terminated by the first empty line after the header fields."
    ),
    "205" =>  array(
      "label" =>  "Reset Content",
      "short" =>  "The server has fulfilled the request and the user agent SHOULD reset the document view which caused the request to be sent.",
      "long"  =>  "The server has fulfilled the request and the user agent SHOULD reset the document view which caused the request to be sent. This response is primarily intended to allow input for actions to take place via user input, followed by a clearing of the form in which the input is given so that the user can easily initiate another input action. The response MUST NOT include an entity."
    ),
    "206" =>  array(
      "label" =>  "Partial Content",
      "short" =>  "The server has fulfilled the partial GET request for the resource.",
      "long"  =>  "The server has fulfilled the partial GET request for the resource. The request MUST have included a Range header field indicating the desired range, and MAY have included an If-Range header field to make the request conditional.\nIf the 206 response is the result of an If-Range request that used a strong cache validator, the response SHOULD NOT include other entity-headers. If the response is the result of an If-Range request that used a weak validator, the response MUST NOT include other entity-headers; this prevents inconsistencies between cached entity-bodies and updated headers. Otherwise, the response MUST include all of the entity-headers that would have been returned with a 200 (OK) response to the same request.\nA cache MUST NOT combine a 206 response with other previously cached content if the ETag or Last-Modified headers do not match exactly.\nA cache that does not support the Range and Content-Range headers MUST NOT cache 206 (Partial) responses."
    ),
    "3xx" =>  array(
      "label" =>  "Redirection",
      "short" =>  "This class of status code indicates that further action needs to be taken by the user agent in order to fulfill the request.",
      "long"  =>  "This class of status code indicates that further action needs to be taken by the user agent in order to fulfill the request. The action required MAY be carried out by the user agent without interaction with the user if and only if the method used in the second request is GET or HEAD. A client SHOULD detect infinite redirection loops, since such loops generate network traffic for each redirection."
    ),
    "300" =>  array(
      "label" =>  "Multiple Choices",
      "short" =>  "The requested resource corresponds to any one of a set of representations, each with its own specific location, and agent- driven negotiation information is being provided.",
      "long"  =>  "The requested resource corresponds to any one of a set of representations, each with its own specific location, and agent- driven negotiation information is being provided so that the user (or user agent) can select a preferred representation and redirect its request to that location.\nUnless it was a HEAD request, the response SHOULD include an entity containing a list of resource characteristics and location(s) from which the user or user agent can choose the one most appropriate. The entity format is specified by the media type given in the Content- Type header field. Depending upon the format and the capabilities of\nthe user agent, selection of the most appropriate choice MAY be performed automatically. However, this specification does not define any standard for such automatic selection.\nIf the server has a preferred choice of representation, it SHOULD include the specific URI for that representation in the Location field; user agents MAY use the Location field value for automatic redirection. This response is cacheable unless indicated otherwise."
    ),
    "301" =>  array(
      "label" =>  "Moved Permanently",
      "short" =>  "The requested resource has been assigned a new permanent URI and any future references to this resource SHOULD use one of the returned URIs.",
      "long"  =>  "The requested resource has been assigned a new permanent URI and any future references to this resource SHOULD use one of the returned URIs. Clients with link editing capabilities ought to automatically re-link references to the Request-URI to one or more of the new references returned by the server, where possible. This response is cacheable unless indicated otherwise.\nThe new permanent URI SHOULD be given by the Location field in the response. Unless the request method was HEAD, the entity of the response SHOULD contain a short hypertext note with a hyperlink to the new URI(s).\nIf the 301 status code is received in response to a request other than GET or HEAD, the user agent MUST NOT automatically redirect the request unless it can be confirmed by the user, since this might change the conditions under which the request was issued."
    ),
    "302" =>  array(
      "label" =>  "Found",
      "short" =>  "The requested resource resides temporarily under a different URI.",
      "long"  =>  "The requested resource resides temporarily under a different URI. Since the redirection might be altered on occasion, the client SHOULD continue to use the Request-URI for future requests. This response is only cacheable if indicated by a Cache-Control or Expires header field.\nThe temporary URI SHOULD be given by the Location field in the response. Unless the request method was HEAD, the entity of the response SHOULD contain a short hypertext note with a hyperlink to the new URI(s).\nIf the 302 status code is received in response to a request other than GET or HEAD, the user agent MUST NOT automatically redirect the request unless it can be confirmed by the user, since this might change the conditions under which the request was issued."
    ),
    "303" =>  array(
      "label" =>  "See Other",
      "short" =>  "The response to the request can be found under a different URI and SHOULD be retrieved using a GET method on that resource.",
      "long"  =>  "The response to the request can be found under a different URI and SHOULD be retrieved using a GET method on that resource. This method exists primarily to allow the output of a POST-activated script to redirect the user agent to a selected resource. The new URI is not a substitute reference for the originally requested resource. The 303 response MUST NOT be cached, but the response to the second (redirected) request might be cacheable.\nThe different URI SHOULD be given by the Location field in the response. Unless the request method was HEAD, the entity of the response SHOULD contain a short hypertext note with a hyperlink to the new URI(s)."
    ),
    "304" =>  array(
      "label" =>  "Not Modified",
      "short" =>  "If the client has performed a conditional GET request and access is allowed, but the document has not been modified, the server SHOULD respond with this status code.",
      "long"  =>  "If the client has performed a conditional GET request and access is allowed, but the document has not been modified, the server SHOULD respond with this status code. The 304 response MUST NOT contain a message-body, and thus is always terminated by the first empty line after the header fields.\nIf the conditional GET used a strong cache validator, the response SHOULD NOT include other entity-headers. Otherwise (i.e., the conditional GET used a weak validator), the response MUST NOT include other entity-headers; this prevents inconsistencies between cached entity-bodies and updated headers.\nIf a 304 response indicates an entity not currently cached, then the cache MUST disregard the response and repeat the request without the conditional.\nIf a cache uses a received 304 response to update a cache entry, the cache MUST update the entry to reflect any new field values given in the response."
    ),
    "305" =>  array(
      "label" =>  "Use Proxy",
      "short" =>  "The requested resource MUST be accessed through the proxy given by the Location field.",
      "long"  =>  "The requested resource MUST be accessed through the proxy given by the Location field. The Location field gives the URI of the proxy. The recipient is expected to repeat this single request via the proxy. 305 responses MUST only be generated by origin servers."
    ),
    "306" =>  array(
      "label" =>  "(Unused)",
      "short" =>  "The 306 status code was used in a previous version of the specification, is no longer used, and the code is reserved.",
      "long"  =>  "The 306 status code was used in a previous version of the specification, is no longer used, and the code is reserved."
    ),
    "307" =>  array(
      "label" =>  "Temporary Redirect",
      "short" =>  "The requested resource resides temporarily under a different URI.",
      "long"  =>  "The requested resource resides temporarily under a different URI. Since the redirection MAY be altered on occasion, the client SHOULD continue to use the Request-URI for future requests. This response is only cacheable if indicated by a Cache-Control or Expires header field.\nThe temporary URI SHOULD be given by the Location field in the response. Unless the request method was HEAD, the entity of the response SHOULD contain a short hypertext note with a hyperlink to the new URI(s) , since many pre-HTTP/1.1 user agents do not understand the 307 status. Therefore, the note SHOULD contain the information necessary for a user to repeat the original request on the new URI.\nIf the 307 status code is received in response to a request other than GET or HEAD, the user agent MUST NOT automatically redirect the request unless it can be confirmed by the user, since this might change the conditions under which the request was issued."
    ),
    "4xx" =>  array(
      "label" =>  "Client Error",
      "short" =>  "The 4xx class of status code is intended for cases in which the client seems to have erred. Except when responding to a HEAD request.",
      "long"  =>  "The 4xx class of status code is intended for cases in which the client seems to have erred. Except when responding to a HEAD request, the server SHOULD include an entity containing an explanation of the error situation, and whether it is a temporary or permanent condition. These status codes are applicable to any request method. User agents SHOULD display any included entity to the user.\nIf the client is sending data, a server implementation using TCP SHOULD be careful to ensure that the client acknowledges receipt of the packet(s) containing the response, before the server closes the input connection. If the client continues sending data to the server after the close, the server's TCP stack will send a reset packet to the client, which may erase the client's unacknowledged input buffers before they can be read and interpreted by the HTTP application."
    ),
    "400" =>  array(
      "label" =>  "Bad Request",
      "short" =>  "The request could not be understood by the server due to malformed syntax.",
      "long"  =>  "The request could not be understood by the server due to malformed syntax. The client SHOULD NOT repeat the request without modifications."
    ),
    "401" =>  array(
      "label" =>  "Unauthorized",
      "short" =>  "The request requires user authentication.",
      "long"  =>  "The request requires user authentication. The response MUST include a WWW-Authenticate header field containing a challenge applicable to the requested resource. The client MAY repeat the request with a suitable Authorization header field. If the request already included Authorization credentials, then the 401 response indicates that authorization has been refused for those credentials. If the 401 response contains the same challenge as the prior response, and the user agent has already attempted authentication at least once, then the user SHOULD be presented the entity that was given in the response, since that entity might include relevant diagnostic information. HTTP access authentication is explained in \"HTTP Authentication: Basic and Digest Access Authentication\"."
    ),
    "402" =>  array(
      "label" =>  "Payment Required",
      "short" =>  "This code is reserved for future use.",
      "long"  =>  "This code is reserved for future use."
    ),
    "403" =>  array(
      "label" =>  "Forbidden",
      "short" =>  "The server understood the request, but is refusing to fulfill it.",
      "long"  =>  "The server understood the request, but is refusing to fulfill it. Authorization will not help and the request SHOULD NOT be repeated. If the request method was not HEAD and the server wishes to make public why the request has not been fulfilled, it SHOULD describe the reason for the refusal in the entity. If the server does not wish to make this information available to the client, the status code 404 (Not Found) can be used instead."
    ),
    "404" =>  array(
      "label" =>  "Not Found",
      "short" =>  "The server has not found anything matching the Request-URI.",
      "long"  =>  "The server has not found anything matching the Request-URI. No indication is given of whether the condition is temporary or permanent. The 410 (Gone) status code SHOULD be used if the server knows, through some internally configurable mechanism, that an old resource is permanently unavailable and has no forwarding address. This status code is commonly used when the server does not wish to reveal exactly why the request has been refused, or when no other response is applicable."
    ),
    "405" =>  array(
      "label" =>  "Method Not Allowed",
      "short" =>  "The method specified in the Request-Line is not allowed for the resource identified by the Request-URI.",
      "long"  =>  "The method specified in the Request-Line is not allowed for the resource identified by the Request-URI. The response MUST include an Allow header containing a list of valid methods for the requested resource."
    ),
    "406" =>  array(
      "label" =>  "Not Acceptable",
      "short" =>  "The resource identified by the request is only capable of generating response entities which have content characteristics not acceptable according to the accept headers sent in the request.",
      "long"  =>  "The resource identified by the request is only capable of generating response entities which have content characteristics not acceptable according to the accept headers sent in the request.\nUnless it was a HEAD request, the response SHOULD include an entity containing a list of available entity characteristics and location(s) from which the user or user agent can choose the one most appropriate. The entity format is specified by the media type given in the Content-Type header field. Depending upon the format and the capabilities of the user agent, selection of the most appropriate choice MAY be performed automatically. However, this specification does not define any standard for such automatic selection.\nIf the response could be unacceptable, a user agent SHOULD temporarily stop receipt of more data and query the user for a decision on further actions."
    ),
    "407" =>  array(
      "label" =>  "Proxy Authentication Required",
      "short" =>  "The client must first authenticate itself with the proxy.",
      "long"  =>  "This code is similar to 401 (Unauthorized), but indicates that the client must first authenticate itself with the proxy. The proxy MUST return a Proxy-Authenticate header field containing a challenge applicable to the proxy for the requested resource. The client MAY repeat the request with a suitable Proxy-Authorization header field. HTTP access authentication is explained in \"HTTP Authentication: Basic and Digest Access Authentication\""
    ),
    "408" =>  array(
      "label" =>  "Request Timeout",
      "short" =>  "The client did not produce a request within the time that the server was prepared to wait.",
      "long"  =>  "The client did not produce a request within the time that the server was prepared to wait. The client MAY repeat the request without modifications at any later time."
    ),
    "409" =>  array(
      "label" =>  "Conflict",
      "short" =>  "The request could not be completed due to a conflict with the current state of the resource.",
      "long"  =>  "The request could not be completed due to a conflict with the current state of the resource. This code is only allowed in situations where it is expected that the user might be able to resolve the conflict and resubmit the request. The response body SHOULD include enough\ninformation for the user to recognize the source of the conflict. Ideally, the response entity would include enough information for the user or user agent to fix the problem; however, that might not be possible and is not required.\nConflicts are most likely to occur in response to a PUT request. For example, if versioning were being used and the entity being PUT included changes to a resource which conflict with those made by an earlier (third-party) request, the server might use the 409 response to indicate that it can't complete the request. In this case, the response entity would likely contain a list of the differences between the two versions in a format defined by the response Content-Type."
    ),
    "410" =>  array(
      "label" =>  "Gone",
      "short" =>  "The requested resource is no longer available at the server and no forwarding address is known.",
      "long"  =>  "The requested resource is no longer available at the server and no forwarding address is known. This condition is expected to be considered permanent. Clients with link editing capabilities SHOULD delete references to the Request-URI after user approval. If the server does not know, or has no facility to determine, whether or not the condition is permanent, the status code 404 (Not Found) SHOULD be used instead. This response is cacheable unless indicated otherwise.\nThe 410 response is primarily intended to assist the task of web maintenance by notifying the recipient that the resource is intentionally unavailable and that the server owners desire that remote links to that resource be removed. Such an event is common for limited-time, promotional services and for resources belonging to individuals no longer working at the server's site. It is not necessary to mark all permanently unavailable resources as \"gone\" or to keep the mark for any length of time -- that is left to the discretion of the server owner."
    ),
    "411" =>  array(
      "label" =>  "Length Required",
      "short" =>  "The server refuses to accept the request without a defined Content- Length.",
      "long"  =>  "The server refuses to accept the request without a defined Content- Length. The client MAY repeat the request if it adds a valid Content-Length header field containing the length of the message-body in the request message."
    ),
    "412" =>  array(
      "label" =>  "Precondition Failed",
      "short" =>  "The precondition given in one or more of the request-header fields evaluated to false when it was tested on the server.",
      "long"  =>  "The precondition given in one or more of the request-header fields evaluated to false when it was tested on the server. This response code allows the client to place preconditions on the current resource metainformation (header field data) and thus prevent the requested method from being applied to a resource other than the one intended."
    ),
    "413" =>  array(
      "label" =>  "Request Entity Too Large",
      "short" =>  "The server is refusing to process a request because the request entity is larger than the server is willing or able to process.",
      "long"  =>  "The server is refusing to process a request because the request entity is larger than the server is willing or able to process. The server MAY close the connection to prevent the client from continuing the request.\nIf the condition is temporary, the server SHOULD include a Retry- After header field to indicate that it is temporary and after what time the client MAY try again."
    ),
    "414" =>  array(
      "label" =>  "Request-URI Too Long",
      "short" =>  "The server is refusing to service the request because the Request-URI is longer than the server is willing to interpret.",
      "long"  =>  "The server is refusing to service the request because the Request-URI is longer than the server is willing to interpret. This rare condition is only likely to occur when a client has improperly converted a POST request to a GET request with long query information, when the client has descended into a URI \"black hole\" of redirection (e.g., a redirected URI prefix that points to a suffix of itself), or when the server is under attack by a client attempting to exploit security holes present in some servers using fixed-length buffers for reading or manipulating the Request-URI."
    ),
    "415" =>  array(
      "label" =>  "Unsupported Media Type",
      "short" =>  "The server is refusing to service the request because the entity of the request is in a format not supported by the requested resource for the requested method.",
      "long"  =>  "The server is refusing to service the request because the entity of the request is in a format not supported by the requested resource for the requested method."
    ),
    "416" =>  array(
      "label" =>  "Requested Range Not Satisfiable",
      "short" =>  "A server SHOULD return a response with this status code if a request included a Range request-header field.",
      "long"  =>  "A server SHOULD return a response with this status code if a request included a Range request-header field, and none of the range-specifier values in this field overlap the current extent of the selected resource, and the request did not include an If-Range request-header field. (For byte-ranges, this means that the first- byte-pos of all of the byte-range-spec values were greater than the current length of the selected resource.)\nWhen this status code is returned for a byte-range request, the response SHOULD include a Content-Range entity-header field specifying the current length of the selected resource. This response MUST NOT use the multipart/byteranges content- type."
    ),
    "417" =>  array(
      "label" =>  "Expectation Failed",
      "short" =>  "The expectation given in an Expect request-header field could not be met by this server.",
      "long"  =>  "The expectation given in an Expect request-header field could not be met by this server, or, if the server is a proxy, the server has unambiguous evidence that the request could not be met by the next-hop server."
    ),
    "5xx" =>  array(
      "label" =>  "Server Error",
      "short" =>  "Response status codes beginning with the digit \"5\" indicate cases in which the server is aware that it has erred or is incapable of performing the request.",
      "long"  =>  "Response status codes beginning with the digit \"5\" indicate cases in which the server is aware that it has erred or is incapable of performing the request. Except when responding to a HEAD request, the server SHOULD include an entity containing an explanation of the error situation, and whether it is a temporary or permanent condition. User agents SHOULD display any included entity to the user. These response codes are applicable to any request method."
    ),
    "500" =>  array(
      "label" =>  "Internal Server Error",
      "short" =>  "The server encountered an unexpected condition which prevented it from fulfilling the request.",
      "long"  =>  "The server encountered an unexpected condition which prevented it from fulfilling the request."
    ),
    "501" =>  array(
      "label" =>  "Not Implemented",
      "short" =>  "The server encountered an unexpected condition which prevented it from fulfilling the request.",
      "long"  =>  "The server encountered an unexpected condition which prevented it from fulfilling the request."
    ),
    "502" =>  array(
      "label" =>  "Bad Gateway",
      "short" =>  "The server, while acting as a gateway or proxy, received an invalid response from the upstream server it accessed in attempting to fulfill the request.",
      "long"  =>  "The server, while acting as a gateway or proxy, received an invalid response from the upstream server it accessed in attempting to fulfill the request."
    ),
    "503" =>  array(
      "label" =>  "Service Unavailable",
      "short" =>  "The server is currently unable to handle the request due to a temporary overloading or maintenance of the server.",
      "long"  =>  "The server is currently unable to handle the request due to a temporary overloading or maintenance of the server. The implication is that this is a temporary condition which will be alleviated after some delay. If known, the length of the delay MAY be indicated in a Retry-After header. If no Retry-After is given, the client SHOULD handle the response as it would for a 500 response."
    ),
    "504" =>  array(
      "label" =>  "Gateway Timeout",
      "short" =>  "The server did not receive a timely response from the upstream server specified by the URI.",
      "long"  =>  "The server, while acting as a gateway or proxy, did not receive a timely response from the upstream server specified by the URI (e.g. HTTP, FTP, LDAP) or some other auxiliary server (e.g. DNS) it needed to access in attempting to complete the request."
    ),
    "505" =>  array(
      "label" =>  "HTTP Version Not Supported",
      "short" =>  "The server does not support, or refuses to support, the HTTP protocol version that was used in the request message.",
      "long"  =>  "The server does not support, or refuses to support, the HTTP protocol version that was used in the request message. The server is indicating that it is unable or unwilling to complete the request using the same major version as the client, other than with this error message. The response SHOULD contain an entity describing why that version is not supported and what other protocols are supported by that server."
    )
  );

  public static function title( $code = null ) {
    if ( !$code ) {
      return false;
    }
    $s  = substr( $code, 0, 1 )."xx";
    if ( isset( self::$codes[$code] ) ) {
      return self::$codes[$code]["label"];
    }
    if ( isset( self::$codes[$s] ) ) {
      return self::$codes[$s]["label"];
    }
    return false;
  }

  public static function info( $code = null ) {
    if ( !$code ) {
      return false;
    }
    $s  = substr( $code, 0, 1 )."xx";
    if ( isset( self::$codes[$code] ) ) {
      return self::$codes[$code]["short"];
    }
    if ( isset( self::$codes[$s] ) ) {
      return self::$codes[$s]["short"];
    }
    return false;
  }

  public static function long_info( $code = null, $html = true ) {
    if ( !$code ) {
      return false;
    }
    $s  = substr( $code, 0, 1 )."xx";
    $info = false;
    if ( isset( self::$codes[$code] ) ) {
      $info = self::$codes[$code]["long"];
    }
    if ( isset( self::$codes[$s] ) ) {
      $info = self::$codes[$s]["long"];
    }
    if ( $html ) {
      if ( $info ) {
        $info = "<p>".implode( "</p>\n<p>", explode( "\n", $info ) )."</p>";
      }
    }
    return $info;
  }
}
?>