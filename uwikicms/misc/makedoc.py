
# UWiKiCMS is a lightweight web content management system.
# Copyright (C) 2005, 2006, 2007, 2009, 2013, 2015 Christian Mauduit <ufoot@ufoot.org>
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License as
# published by the Free Software Foundation; either version 2 of
# the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public
# License along with this program; if not, write to the Free
# Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,
# MA  02110-1301  USA

# Got this script from Liquid War, pathed to be able
# to transform XML docs into uwikicms content.

import xmllib
import string
import re

def remove_duplicate_blanks(text):
    result=text

    result=string.replace(result,"\t"," ")
    result=string.replace(result,"\n"," ")

    if (result!=""):
        temp=""
        while temp!=result:
            temp=result
            result=string.replace(result,"  "," ")

    return result

def format_text(text,left_col,right_col):
    temp=remove_duplicate_blanks(text)

    result=""
    p=0
    l=len(temp)
    while p<l:
        cur_line=temp[p:p+right_col-left_col]
        dp=len(cur_line)
        if dp==right_col-left_col:
            i=dp-1
            while i>0:
                if cur_line[i]==' ':
                    break
                i=i-1
            if i>0:
                cur_line=cur_line[0:i]
                dp=i+1
                if 0==1: #the following code justifies the text, it is disabled
                    j=i-1
                    while len(cur_line)<right_col-left_col-1:
                        if cur_line[j]==' ':
                            while j>0 and cur_line[j-1]==' ':
                                j=j-1
                                cur_line=cur_line[:j]+' '+cur_line[j:]
                                j=j-1
                            if j<0:
                                j=len(cur_line)-1
                    
        result=result+" "*left_col+cur_line+"\n"
        p=p+dp
    return result

def format_email_and_url(text):
    result=text	

    email=re.compile('"([\w\-\.]+@[\w\-\.]+)"')
    result=email.sub(r'\1',result);

    url=re.compile('"http://([\w\-\.\~/]+)"', re.I)
    result=url.sub(r'http://\1',result);

    return result

def format_html(text):
    result=text

    result=string.replace(result,"<","ufoot_html_lt")
    result=string.replace(result,">","ufoot_html_gt")
    result=string.replace(result,"&","&amp;")
    result=string.replace(result,"ufoot_html_lt","&lt;")
    result=string.replace(result,"ufoot_html_gt","&gt;")    

    # Uncomment this to make mailing list adresses look like "xxx at xxx"
    # instead of "xxx@xxx". This can prevent spammers from harvesting
    # the mailing list address
    # fakeemail=re.compile('"([\w\.]+\-user)@([\w\-\.]+)"')
    # result=fakeemail.sub(r'<A HREF="mailto:\1 at \2">\1 at \2</A> (replace "at" by "@")',result);

    email=re.compile('"([\w\-\.]+@[\w\-\.]+)"')
    result=email.sub(r'<A HREF="mailto:\1">\1</A>',result);

    url=re.compile('"http://([\w\-\.\~/]+)"', re.I)
    result=url.sub(r'<A HREF="http://\1">\1</A>',result);
    
    return result

def format_tex(text):
    result=text

    result=string.replace(result,"\\","$\\backslash$")
    result=string.replace(result,"_","\\_")
    result=string.replace(result,"#","\\#")
    result=string.replace(result,"%","\\%")
    result=string.replace(result,"}","\\}")
    result=string.replace(result,"<","$<$")
    result=string.replace(result,">","$>$")
    result=string.replace(result,"~","$\\tilde{}$")
    
    return result

def format_texi(text):
    result=text

    result=string.replace(result,"@","@@")
    result=string.replace(result,"}","@}")
    result=string.replace(result,"{","@{")

    return result

def format_uwc(text):
    result=text

    result=string.replace(result,"]","]")
    result=string.replace(result,"[","[[")

    return result

def format_uwc_text(text):
    result=text

    result=format_uwc(result)
    
    # This is an ugly way to get rid of all junk at line start
    result=format_text(result,0,999999)

    return result

def format_uwc_elem(text):
    result=text

    result=format_uwc(result)
    result=string.replace(result,"\n"," ")
    result=string.replace(result,"\r"," ")
    result=remove_duplicate_blanks(result)

    return result

def format_uwc_code(text):
    result=text

    result=format_uwc(result)
    line=re.compile('^(.*)$',re.M)
    result=line.sub(r"  \1",result);

    return result

class XMLToX(xmllib.XMLParser):
    def __init__(self):
        xmllib.XMLParser.__init__(self)
        self.translated=""
        self.elements["file"]=(None,None)
        self.elements["chap"]=(None,None)
        self.elements["part"]=(None,None)
        self.elements["text"]=(None,None)
        self.elements["list"]=(None,None)
        self.elements["elem"]=(None,None)
        self.elements["code"]=(None,None)
    def write(self,text):
        self.translated=self.translated+text
    def start_file(self,title):
        pass
    def start_chap(self,title):
        pass
    def start_part(self,title):
        pass
    def start_text(self):
        pass
    def start_list(self):
        pass
    def start_elem(self):
        pass
    def start_code(self):
        pass
    def end_file(self):
        pass
    def end_chap(self):
        pass
    def end_part(self):
        pass
    def end_text(self):
        pass
    def end_list(self):
        pass
    def end_elem(self):
        pass
    def end_code(self):
        pass
    def unknown_starttag(self,tag,attributes):
        if tag=="file":
            self.start_file(attributes["title"])
        if tag=="chap":
            self.start_chap(attributes["title"])
        if tag=="part":
            self.start_part(attributes["title"])
        if tag=="text":
            self.start_text()
        if tag=="list":
            self.start_list()
        if tag=="elem":
            self.start_elem()
        if tag=="code":
            self.start_code()
    def unknown_endtag(self,tag):
        if tag=="file":
            self.end_file()
        elif tag=="chap":
            self.end_chap()
        elif tag=="part":
            self.end_part()
        elif tag=="text":
            self.end_text()
        elif tag=="list":
            self.end_list()
        elif tag=="elem":
            self.end_elem()
        elif tag=="code":
            self.end_code()
    def translate(self,data,tag):
        return data
    def handle_data(self,data):
        if len(self.stack)>0:
            data=string.strip(data)
            if (data!=''):
                #print data
                #print self.stack
                self.write(self.translate(data,self.stack[-1][0]))

class XMLToHTML(XMLToX):
    def __init__(self,header,footer):
        XMLToX.__init__(self)
        self.header=header
        self.footer=footer
    def start_file(self,title):
        self.write("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 3.2//EN\">\n"+\
                   "<HTML>\n<HEAD>\n"+\
                   "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=iso-8859-1\">\n"+\
                   "<TITLE>"+\
                   title+\
                   "</TITLE>\n</HEAD>\n<BODY>\n\n"+\
                   self.header+\
                   "\n<CENTER><H1>"+\
                   title+\
                   "</H1></CENTER>\n")
    def start_chap(self,title):
        self.write("<HR>\n<H2>"+\
                   title+\
                   "</H2>\n")
    def start_part(self,title):
        self.write("<H3>"+\
                   title+\
                   "</H3>\n")
    def start_text(self):
        self.write("<P>")
    def start_list(self):
        self.write("<UL>")
    def start_elem(self):
        self.write("<LI>")
    def start_code(self):
        self.write("<PRE>")
    def end_file(self):
        self.write(self.footer+\
                   "\n</BODY>\n</HTML>\n")
    def end_chap(self):
        self.write("\n")
    def end_part(self):
        self.write("\n")
    def end_text(self):
        self.write("</P>\n")
    def end_list(self):
        self.write("</UL>\n")
    def end_elem(self):
        self.write("</LI>\n")
    def end_code(self):
        self.write("</PRE>\n")
    def translate(self,data,tag):
        result=data
        result=format_html(result)
        return result

class XMLToPHP3(XMLToX):
    def __init__(self):
        XMLToX.__init__(self)
    def start_file(self,title):
        self.write("\n\n<!-- www.ufoot.org : page begins here -->\n\n"+\
                   "<center>\n<h1>"+\
                   title+\
                   "</h1>\n</center>\n")
    def start_chap(self,title):
        self.write("<hr />\n<h2>"+\
                   title+\
                   "</h2>\n")
    def start_part(self,title):
        self.write("<h3>"+\
                   title+\
                   "</h3>\n")
    def start_text(self):
        self.write("<p>")
    def start_list(self):
        self.write("<ul>")
    def start_elem(self):
        self.write("<li>")
    def start_code(self):
        self.write("<pre>")
    def end_file(self):
        self.write("\n\n<!-- www.ufoot.org : page ends here -->\n\n")
    def end_chap(self):
        self.write("\n")
    def end_part(self):
        self.write("\n")
    def end_text(self):
        self.write("</p>\n")
    def end_list(self):
        self.write("</ul>\n")
    def end_elem(self):
        self.write("</li>\n")
    def end_code(self):
        self.write("</pre>\n")
    def translate(self,data,tag):
        result=data
        result=format_html(result)
        return result

class XMLToTeX(XMLToX):
    def __init__(self):
        XMLToX.__init__(self)
    def start_file(self,title):
        self.write("\\chapter{"+\
                   self.translate(title,"text")+\
                   "}\n")
    def start_chap(self,title):
        self.write("\\section{"+\
                   self.translate(title,"text")+\
                   "}\n")
    def start_part(self,title):
        self.write("\\subsection{"+\
                   self.translate(title,"text")+\
                   "}\n")
    def start_text(self):
        self.write("\n")
    def start_list(self):
        self.write("\\begin{itemize}\n")
    def start_elem(self):
        self.write("\\item[$\\bullet$]")
    def start_code(self):
        self.write("\\begin{verbatim}\n")
    def end_file(self):
        self.write("")
    def end_chap(self):
        self.write("\n")
    def end_part(self):
        self.write("\n")
    def end_text(self):
        self.write("\n")
    def end_list(self):
        self.write("\\end{itemize}\n")
    def end_elem(self):
        self.write("\n")
    def end_code(self):
        self.write("\n\\end{verbatim}\n")
    def translate(self,data,tag):
        result=data
	result=format_email_and_url(result)
        if (tag!="code"):
            result=format_tex(result)
        return result

class XMLToMan(XMLToX):
    def __init__(self):
        XMLToX.__init__(self)
    def start_file(self,title):
        self.write(".SH "+title+"\n\n")
    def start_chap(self,title):
        self.write(".SS "+title+"\n\n")
    def start_part(self,title):
        self.write(".TP 0 \n.B "+title+"\n")

    def start_text(self):
        self.write(".HP 0\n")
    def start_list(self):
        self.write("")
    def start_elem(self):
        self.write(".TP 3 \n.B *\n")
    def start_code(self):
        self.write(".HP 0\n")
    def end_file(self):
        self.write("")
    def end_chap(self):
        self.write("\n")
    def end_part(self):
        self.write("\n")
    def end_text(self):
        self.write("\n")
    def end_list(self):
        self.write("\n")
    def end_elem(self):
        self.write("\n")
    def end_code(self):
        self.write("\n")
    def translate(self,data,tag):
        result=data
	result=format_email_and_url(result)
        result=string.replace(result,"\\","\\\\")
        result=string.replace(result,".","\.")
        result=string.replace(result,"-","\-")
        if (tag=="code"):
            result=string.replace(result,"\n","\n.br\n")
        else:
            result=remove_duplicate_blanks(result)
        return result

class XMLToTxt(XMLToX):
    def __init__(self,header):
        XMLToX.__init__(self)
        self.header=header
    def start_file(self,title):
        self.write(self.header+" - "+title+"\n\n")
        self.indent=0
    def start_chap(self,title):
        self.write("\n\n"+\
                   " "*self.indent+"\n"+title+"\n"+\
                   " "*self.indent+"="*len(title)+"\n\n")
        self.indent=self.indent+2
    def start_part(self,title):
        self.write("\n"+\
                   " "*self.indent+title+"\n"+\
                   " "*self.indent+"-"*len(title)+"\n")
        self.indent=self.indent+2
    def start_text(self):
        self.write("\n")
    def start_list(self):
        self.write("")
        self.indent=self.indent+2
    def start_elem(self):
        self.write("\n")
    def start_code(self):
        self.write("\n")
    def end_file(self):
        self.write("\n")
    def end_chap(self):
        self.write("")
        self.indent=self.indent-2
    def end_part(self):
        self.write("")
        self.indent=self.indent-2
    def end_text(self):
        self.write("")
    def end_list(self):
        self.write("")
        self.indent=self.indent-2
    def end_elem(self):
        self.write("")
    def end_code(self):
        self.write("\n")
    def translate(self,data,tag):
        result=data
	result=format_email_and_url(result)
        if (tag=="code"):
            result=" "*self.indent+\
                    string.replace(result,"\n","\n"+" "*self.indent)
        else:
            result=format_text(result,self.indent,80)
            if (tag=="elem"):
                result=" "*(self.indent-2)+"* "+result[self.indent:]
        return result

class XMLToTexi(XMLToX):
    def __init__(self,node):
        XMLToX.__init__(self)
        self.node=node
    def start_file(self,title):
        self.write("\n@node "+self.node+" , , , Top\n")
        self.write("\n@chapter "+title+"\n");
    def start_chap(self,title):
        self.write("\n@section "+title+"\n");
    def start_part(self,title):
        self.write("\n@subsection "+title+"\n");
    def start_text(self):
        self.write("\n")
    def start_list(self):
        self.write("\n@itemize @bullet")
    def start_elem(self):
        self.write("\n@item\n")
    def start_code(self):
        self.write("\n@example\n")
    def end_file(self):
        self.write("\n")
    def end_chap(self):
        self.write("\n")
    def end_part(self):
        self.write("\n")
    def end_text(self):
        self.write("\n")
    def end_list(self):
        self.write("@end itemize\n")
    def end_elem(self):
        self.write("\n")
    def end_code(self):
        self.write("\n@end example\n")
    def translate(self,data,tag):
        result=data
	result=format_email_and_url(result)

        if (tag!="code"):
            result=remove_duplicate_blanks(result)
        result=format_texi(result)
        return result

class XMLToUWC(XMLToX):
    def __init__(self):
        XMLToX.__init__(self)
    def start_file(self,title):
        # In UWikKiCMS document is handled manually once for
        # all directly in the UWiKiCMS site/instance.
        pass
    def start_chap(self,title):
        self.write("\n!! "+title+"\n");
    def start_part(self,title):
        self.write("\n! "+title+"\n");
    def start_text(self):
        self.write("\n")
    def start_list(self):
        self.write("\n")
    def start_elem(self):
        self.write("\n* ")
    def start_code(self):
        self.write("\n")
    def end_file(self):
        self.write("\n")
    def end_chap(self):
        self.write("\n")
    def end_part(self):
        self.write("\n")
    def end_text(self):
        self.write("\n")
    def end_list(self):
        self.write("\n")
    def end_elem(self):
        # No \n for we do not want blank lines between elems
        pass
    def end_code(self):
        self.write("\n")
    def translate(self,data,tag):
        result=data
	result=format_email_and_url(result)

        if (tag=="code"):
            result=format_uwc_code(result)
        elif (tag=="elem"):
            result=format_uwc_elem(result)
        else:
            result=format_uwc_text(result)        

        return result

def run_parser(parser,dst,src):
    dst_file=open(dst,"w")
    src_file=open(src,"r")
    src_code=src_file.read()
    parser.feed(src_code)
    dst_code=parser.translated
    dst_file.write(dst_code)
    src_file.close()
    dst_file.close()
        
def make_html(html,xml,header,footer):
    header_file=open(header,"r")
    footer_file=open(footer,"r")
    header_str=header_file.read()
    footer_str=footer_file.read()
    parser=XMLToHTML(header_str,footer_str)
    run_parser(parser,html,xml)
    header_file.close()
    footer_file.close()

def make_php3(php3,xml):
    parser=XMLToPHP3()
    run_parser(parser,php3,xml)

def make_tex(tex,xml):
    parser=XMLToTeX()
    run_parser(parser,tex,xml)

def make_man(man,xml):
    parser=XMLToMan()
    run_parser(parser,man,xml)

def make_txt(txt,xml,header):
    parser=XMLToTxt(header)
    run_parser(parser,txt,xml)

def make_texi(texi,xml):
    node=string.replace(xml,".xml","")
    node=string.replace(node,"xml/","")
    parser=XMLToTexi(node)
    run_parser(parser,texi,xml)

def make_uwc(uwc,xml):
    parser=XMLToUWC()
    run_parser(parser,uwc,xml)








