# parser
 Simple webSite Parser. 
 I have tested this my parser with both HTTP-protocol and HTTPS too. 
 Works such features: 
 1. Following some links. That is, the aproppriate webpage will be opened when the link is clicked (as usual). But, unfortunately, this isnot true for alll links. 
 2. Getting such resourses as: <img ... />, <script... ></script>, <link ... /> from a server. Wherein src or href may be absolute, relative or starting from the site root (relative to site root).

You need to enter it URL in the entery field and then click "Старт" to parse the webpage. You should  also click "Старт" after clicking the link to enter the another webpage  too. The main file is "parser.php". That is the URL in Your brouser may be something as http://yoursite.com/parser.php.
