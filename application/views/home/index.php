<!doctype html>
<html>

<head>
    <title>Fitmissive</title>
    <link rel="stylesheet" href="/css/index-styles.css" type="text/css">
</head>

<body>
    <header class="header">
        <img src="/img/logo-transparent.png" alt="error" width="400px" height="60px">
    </header>
    <form method="POST" action="/home/registerAction">
        <div class="regForm">
            <table>
                <tr>
                    <td colspan="2" style="text-align:center;">
                        <legend class="title">Register here if you don't have an account.</legend>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="username">Username:</label>
                    </td>
                    <td>
                        <input type="text" name="usernameReg" id="username" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="email">Email:</label>
                    </td>
                    <td>
                        <input type="text" name="email" id="email" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="password">Password:</label>
                    </td>
                    <td>
                        <input type="password" name="passwordReg" id="password" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="password2">Repeat password:</label>
                    </td>
                    <td>
                        <input type="password" name="password2" id="password2" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;">
                        <input type="submit" name="submitRegistration" value="Register">
                    </td>
                </tr>
            </table>
        </div>
    </form>
    <form method="POST" action="/home/loginAction">
        <div class="loginForm">
            <table>
                <tr>
                    <td colspan="2" style="text-align:center;">
                        <legend class="title">Already have and account? Log in!</legend>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="username">Username:</label>
                    </td>
                    <td>
                        <input type="text" name="username" id="username" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="password">Password:</label>
                    </td>
                    <td>
                        <input type="password" name="password" id="password" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;">
                        <input type="submit" name="submitLogin" value="Login">
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center; padding: 15px;">
                        Forgot your password?
                        <a href="forgot-password.php">Click here to reset it.</a>
                    </td>
                </tr>
            </table>
        </div>
    </form>
    <footer class="footer">
        <table>
            <tr>
                <td>
                    <a href="">Contacts</a> | <a href="">Home</a> | <a href="">About Us</a> |
                    <a href="">Privacy policy</a> | <a href="">Terms & Conditions</a>
                </td>
            </tr>
            <tr>
                <td style="text-align:center;">
                    All rights reserved 2022
                </td>
            </tr>
        </table>
    </footer>
</body>

</html>