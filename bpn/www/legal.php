<?php require("system/shared.php"); ?>
<?php $title=" - Legal"; require("header.php"); ?>
<body>
    <?php topbar("legal"); ?>

    <div class="container">
        <div class="content">
            <div class="page-header">
                <h1>Legal</h1>
            </div>
            <div class="row">
                <div class="span10">
		First of all BPN cannot know what the payments monitored are for, nor do we request that our users tell us, it could be napkins or something worse.<br>
                    <br>
		We cannot identify who sent or received a payment, other than what is publicly known in the bitcoin blockchain.<br>
                    <br>
		Requesting a monitor on BPN does not prove that the address is acutally owned by this individual.<br>
		As soon as an address have received 1 payment, it is publicly known in the block chain.<br>
                    <br>
		We log which IP signs in to which account for general security measures, as well as which pages are seen by which IP.<br>
		We make no attempt to prove if this is the actual IP of the user, or a proxy/tor/vpn/etc.<br>
                    <br>
		Given a court order we will release all known information about an monitor account, including, but not limited to, username, hashed password, monitored addresses, HTTP POST targets.
                    <br><br>
		Before contacting us, please consider than anyone who wants to monitor addresses, that may be sensitive could easily download the project and run it themselves, and thereby publish nothing more than they are downloading the blockchain.
                    <br><br>

                    <h1>License</h1>
<PRE>
This is free and unencumbered software released into the public domain.

Anyone is free to copy, modify, publish, use, compile, sell, or
distribute this software, either in source code form or as a compiled
binary, for any purpose, commercial or non-commercial, and by any
means.

In jurisdictions that recognize copyright laws, the author or authors
of this software dedicate any and all copyright interest in the
software to the public domain. We make this dedication for the benefit
of the public at large and to the detriment of our heirs and
successors. We intend this dedication to be an overt act of
relinquishment in perpetuity of all present and future rights to this
software under copyright law.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY CLAIM, DAMAGES OR
OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.

For more information, please refer to <a href="http://unlicense.org/" target="_blank">http://unlicense.org</a>
</PRE>
                </div>
                <?php require("system-status.php"); ?>
            </div>
            <?php require("footer.php"); ?>
        </div>
</body>
</html>
