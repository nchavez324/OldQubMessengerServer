QubMessengerServer
==================

Server backend for Qub Messenger iOS App

A PHP RESTful API for the iOS Qub Messenger app. I wrote this, along with the client app over the summer of 2013.
I wanted to learn to do iOS programming and further develop my server side skills, so I developed this as well.
It primarily interacts with a mySQL server, where user data, including their profile pictures, connections and
messages are stored. So far, it only suppports GET requests, so the client is pretty static as a result, though
it clearly pulls all of its data from the sever.

One of the most challenging things to do was organize the data in the SQL database and figure out the class
structure of the API. I ended up having the image data stored in the filesystem, with the database referencing it.
I also had friends/connections organized by id, with the id of one user, the id of the second and a status (requested,
confirmed, none).

I wrote this using Sublime Text and Filezilla! I believe it requires PHP 5.3.

====================
The MIT License (MIT) Copyright © 2014 Nick Chavez

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the “Software”), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
