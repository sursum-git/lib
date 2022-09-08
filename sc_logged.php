<?php
//__NM____NM__FUNCTION__NM__//

	function sc_logged($user, $ip = '')
	{
		$str_sql = "SELECT date_login, ip FROM sec_logged WHERE login = ". sc_sql_injection($user) ." AND sc_session <> ".sc_sql_injection('_SC_FAIL_SC_');

		sc_select(data, $str_sql);

		if({data} === FALSE || !isset($data->fields[0]))
		{
            $ip = ($ip == '') ? $_SERVER['REMOTE_ADDR'] : $ip;
			sc_logged_in($user, $ip);
			return true;
		}
		else
		{
            sc_reset_apl_conf("app_logged");
            sc_apl_status("app_logged", 'on');
			sc_redir("app_logged", user=$user, 'modal');
			return false;
		}
	}

	function sc_verify_logged()
	{
		$str_sql = "SELECT count(*) FROM sec_logged WHERE login = ". sc_sql_injection([logged_user]) . " AND date_login = ". sc_sql_injection([logged_date_login]) ." AND sc_session <> ".sc_sql_injection('_SC_FAIL_SC_');
		sc_lookup(rs_logged, $str_sql);
		if({rs_logged[0][0]} != 1)
		{
			sc_redir("app_Login","","_parent");
		}
	}

    function sc_logged_in($user, $ip = '')
	{
        $ip = ($ip == '') ? $_SERVER['REMOTE_ADDR'] : $ip;
		[logged_user] = $user;
		[logged_date_login] = microtime(true);

        $str_sql = "DELETE FROM sec_logged WHERE login = ". sc_sql_injection($user) . " AND sc_session = ".sc_sql_injection('_SC_FAIL_SC_')." AND ip = ".sc_sql_injection($ip);
        sc_exec_sql($str_sql);

    	$str_sql = "INSERT INTO sec_logged(login, date_login, sc_session, ip) VALUES (". sc_sql_injection($user) .", ". sc_sql_injection([logged_date_login]) .", ". sc_sql_injection(session_id()) .", ". sc_sql_injection($ip) .")";
	    sc_exec_sql($str_sql);
	}

    function sc_logged_in_fail($user, $ip = '')
    {
        $ip = ($ip == '') ? $_SERVER['REMOTE_ADDR'] : $ip;
        $user = sc_sql_injection($user);
        $str_sql = "INSERT INTO sec_logged(login, date_login, sc_session, ip) VALUES (" . sc_sql_injection($user) . ", " . sc_sql_injection(microtime(true)) . ", ". sc_sql_injection('_SC_FAIL_SC_').", " . sc_sql_injection($ip) . ")";
        sc_exec_sql($str_sql);
        return true;

    }

    function sc_logged_is_blocked($ip = '')
    {
        $ip = ($ip == '') ? $_SERVER['REMOTE_ADDR'] : $ip;
        $minutes_ago = strtotime("-10 minutes");
        $str_select = "SELECT count(*) FROM sec_logged WHERE sc_session = ".sc_sql_injection('_SC_BLOCKED_SC_')." AND ip = ".sc_sql_injection($ip)." AND date_login > ". sc_sql_injection($minutes_ago);
        sc_lookup(rs_logged, $str_select);
        if({rs_logged} !== FALSE && {rs_logged[0][0]} == 1)
        {
            $message = {lang_user_blocked};
                $message = sprintf($message, 10);
                sc_error_message($message);
                return true;
        }

        $str_select = "SELECT count(*) FROM sec_logged WHERE sc_session = ".sc_sql_injection('_SC_FAIL_SC_')." AND ip = ".sc_sql_injection($ip)." AND date_login > ". sc_sql_injection($minutes_ago);
        sc_lookup(rs_logged, $str_select);

        if({rs_logged} !== FALSE && {rs_logged[0][0]} == 10)
        {
            $str_sql = "INSERT INTO sec_logged(login, date_login, sc_session, ip) VALUES (".sc_sql_injection('blocked').", ". sc_sql_injection(microtime(true)) .", ".sc_sql_injection('_SC_BLOCKED_SC_'). ", ". sc_sql_injection($ip) .")";
            sc_exec_sql($str_sql);
            $message = {lang_user_blocked};
                $message = sprintf($message, 10);
                sc_error_message($message);
                return true;
        }
        return false;

    }


    function sc_logged_out($user, $date_login = '')
	{
		$date_login = ($date_login == '' ? "" : " AND date_login = ". sc_sql_injection($date_login) ."");

		$str_sql = "SELECT sc_session FROM sec_logged WHERE login = ". sc_sql_injection($user) ." ". $date_login . " AND sc_session <> ".sc_sql_injection('_SC_FAIL_SC_');
		sc_lookup(data, $str_sql);
		if(isset({data[0][0]}) && !empty({data[0][0]}))
        {
			$session_bkp = $_SESSION;
			$sessionid = session_id();
			session_write_close();

			session_id({data[0][0]});
			session_start();
			$_SESSION['logged_user'] = 'logout';
			session_write_close();

			session_id($sessionid);
			session_start();
			$_SESSION = $session_bkp;
		}


		$str_sql = "DELETE FROM sec_logged WHERE login = ". sc_sql_injection($user) . " " . $date_login;
		sc_exec_sql($str_sql);
		sc_reset_global([logged_date_login], [logged_user]);
	}
    function sc_looged_check_logout()
    {
        if(isset([logged_user]) && ([logged_user] == 'logout' || empty([logged_user])))
        {
            sc_reset_global ([usr_login], [logged_user], [logged_date_login], [usr_email]);
        }
    }

?>