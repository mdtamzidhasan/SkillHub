<?php

return $settings = array(

    /**
     * Array of IDP prefixes to be configured e.g. 'idpNames' => ['test1', 'test2', 'test3'],
     * Separate routes will be automatically registered for each IDP specified with IDP name as prefix
     * Separate config file saml2/<idpName>_idp_settings.php should be added & configured accordingly
     */
    'idpNames' => ['test'],

    /**
     * If 'useRoutes' is set to true, the package defines five new routes for reach entry in idpNames:
     *
     *    Method | URI                                | Name
     *    -------|------------------------------------|------------------
     *    POST   | {routesPrefix}/{idpName}/acs       | saml_acs
     *    GET    | {routesPrefix}/{idpName}/login     | saml_login
     *    GET    | {routesPrefix}/{idpName}/logout    | saml_logout
     *    GET    | {routesPrefix}/{idpName}/metadata  | saml_metadata
     *    GET    | {routesPrefix}/{idpName}/sls       | saml_sls
     */
    'useRoutes' => true,

    /**
     * Optional, leave empty if you want the defined routes to be top level, i.e. "/{idpName}/*"
     */
    'routesPrefix' => '/saml2',

    /**
     * which middleware group to use for the saml routes
     * Laravel 5.2 will need a group which includes StartSession
     */
    'routesMiddleware' => [],

    /**
     * Indicates how the parameters will be
     * retrieved from the sls request for signature validation
     */
    'retrieveParametersFromServer' => false,

    /**
     * Where to redirect after logout
     */
    'logoutRoute' => '/',

    /**
     * Where to redirect after login if no other option was provided
     */
    'loginRoute' => '/',

    /**
     * Where to redirect after login if no other option was provided
     */
    'errorRoute' => '/',

    // If 'proxyVars' is True, then the Saml lib will trust proxy headers
    // e.g X-Forwarded-Proto / HTTP_X_FORWARDED_PROTO. This is useful if
    // your application is running behind a load balancer which terminates
    // SSL.
    'proxyVars' => false,

    /**
     * (Optional) Which class implements the route functions.
     * If commented out, defaults to this lib's controller (Aacotroneo\Saml2\Http\Controllers\Saml2Controller).
     * If you need to extend Saml2Controller (e.g. to override the `login()` function to pass
     * a `$returnTo` argument), this value allows you to pass your own controller, and have
     * it used in the routes definition.
     */
     // 'saml2_controller' => '',
        'routesMiddleware' => ['web'],

     'idp' => [
    'entityId' => 'http://www.okta.com/exk12g3ovz2UhDsP4698',
    'singleSignOnService' => [
        'url' => 'https://trial-4525538.okta.com/app/trial-4525538_skillhubsaml_1/exk12g3ovz2UhDsP4698/sso/saml',
        'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'singleLogoutService' => [
        'url' => 'https://trial-4525538.okta.com/app/trial-4525538_skillhubsaml_1/exk12g3ovz2UhDsP4698/slo/saml',
        'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    ],
    'x509cert' => 'MIIDqjCCApKgAwIBAgIGAZ3Yq7/NMA0GCSqGSIb3DQEBCwUAMIGVMQswCQYDVQQGEwJVUzETMBEG
A1UECAwKQ2FsaWZvcm5pYTEWMBQGA1UEBwwNU2FuIEZyYW5jaXNjbzENMAsGA1UECgwET2t0YTEU
MBIGA1UECwwLU1NPUHJvdmlkZXIxFjAUBgNVBAMMDXRyaWFsLTQ1MjU1MzgxHDAaBgkqhkiG9w0B
CQEWDWluZm9Ab2t0YS5jb20wHhcNMjYwNDI5MDk1NjA3WhcNMzYwNDI5MDk1NzA3WjCBlTELMAkG
A1UEBhMCVVMxEzARBgNVBAgMCkNhbGlmb3JuaWExFjAUBgNVBAcMDVNhbiBGcmFuY2lzY28xDTAL
BgNVBAoMBE9rdGExFDASBgNVBAsMC1NTT1Byb3ZpZGVyMRYwFAYDVQQDDA10cmlhbC00NTI1NTM4
MRwwGgYJKoZIhvcNAQkBFg1pbmZvQG9rdGEuY29tMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIB
CgKCAQEAm9K3W8hyVdap4nV7Ng3HPRoHiMJF1AB/ZTrN7IHR6oQsqGh0lgp6VpkF1ix/mHQZi7mY
isQX1evcrHB7ovfWGNIS78+XuqIb0CdiO4n2sgC7mNDSkFA5drsdCL/ctqthws8OOsDvPMbYBM+h
9mhh2a5WQfCnyj6WI+tlN7KbW0lzSfCffz+5DFcMDpgQg39BnNKomQFbxNWact/5jlNmGAWhmcG0
zz0PGPSYIPZH3kn3OBCOUD0y1G7+NiBSel7ijm4H6HnLzbwbWmpkUOokkpJtjw2WPmoozM78SyyC
BYLwv+huxVUSwxv77ZVXnlD5b2somqSFJH+MNR7riHXn8QIDAQABMA0GCSqGSIb3DQEBCwUAA4IB
AQAKl0mj9PvMdoP53eCZ/cW8BrPAa0Vv0QyeuNGPn2FwhVjqv0niZiyrRW6iqv+bcDRlC2BUg70y
nUnzujc370X6Kd16PTBrjRbaRgVjUgOy+NN6tZM8X7FmmUaIPk/GlinwVXbTxeu0i3az2sBrKNY3
QoU2JpVxbXWZnb/hbDRTPTo6oWYYor2/AOl1gdO7oUdGedjybYUbtxhVOv8XEQtnR9GNubxmxLYB
OTpQhemHvBggKb4VNl9V4A4Y0DtyRwsmfJhp7wW8ixrX3G9mU0e3+I52t1BJJIkasenLssCPcyME
eq6Z3S8l8muecij8miloh9omTeLwLJge5lDad/af',
],

  'sp' => [
    'entityId' => 'https://encore-lard-helmet.ngrok-free.dev/saml2/test/metadata',
    'assertionConsumerService' => [
        'url' => 'https://encore-lard-helmet.ngrok-free.dev/saml2/test/acs',
    ],
    'singleLogoutService' => [
        'url' => 'https://encore-lard-helmet.ngrok-free.dev/saml2/test/sls',
    ],
],
);
