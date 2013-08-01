<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Go Contact Center - Últimos Contactos</title>


        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/style.css" />
        <?php
        $user = $_GET["user"];
        require("dbconnect.php");


        $list = mysql_query("SELECT call_date,first_name,a.phone_number,b.comments FROM `vicidial_log` a inner join `vicidial_list` b on a.lead_id=b.lead_id WHERE a.user like '$user' order by call_date DESC") or die(mysql_error());
        ?>
    <body>

        <div class="grid-transparent">
            <div class="grid-title">Últimos Contactos</div>

                <table class="table table-striped table-mod">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nome Cliente</th>
                            <th>Nº Telefone</th>
                            <th>Comentários</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($rlist = mysql_fetch_assoc($list)) { ?>
                            <tr>
                                <td><?= $rlist[call_date] ?></td>
                                <td><?= $rlist[first_name] ?> </td>
                                <td><?= $rlist[phone_number] ?></td>
                                <td><?= $rlist[comments] ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
        </div>

    </form>
</body>

<p class="text-center">
    <a onclick="window.close()" href="#">Fechar Janela</a>
</p>
</body>
</html>