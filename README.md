#Description
This package facilitates parsing and reading configurations from ini files.
To create a hierarchical ini file, the only thing to do is to write keys seperated by a delimiter
(by default '.') to specify parent and child. For example:

    [application]
    title="title"
    db.username="username"
    db.password="123"

You can also use a value by it's key in another configuration value:

    [application]
    url=http://localhost/my-app
    login=${url}/login

Note: ${url} assumes *url* key is in the current section, it can be written like ${application.url}.
If you want to refer to ${url} in another section (e.g. global), you should mention it as a prefix e.g. ${global.url}.  
If you are using multi-level configuration keys (e.g. application.config.db.username), write the root as prefix, e.g. ${*prefix*.variable}.

#Features
- Easy to use
- Convenient Integration
- Support for hierarchical data structure
- Array configs
- Key-value binding

#How to use
Pass the ini configuration path to the *parse* method:

    $ini = PHProp::parse("path/to/ini");

If your scope delimiter is not '.', give your delimiter as the second parameter:

    $ini = PHProp::parse("path/to/ini", "/");

After getting the object, the username can be accessed easily:

    $ini->application->db->username 

or

    $ini['application']['db']['username']

To get the number of application's children:

    count($ini->application);

See the examples for more...
