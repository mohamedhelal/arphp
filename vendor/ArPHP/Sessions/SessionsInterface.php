<?php
/**
 * -----------------------------------
 * File  : SessionsInterface.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace ArPHP\Sessions;


interface SessionsInterface
{
    /**
     * create first session
     * @param $data
     * @return mixed
     */
    public function create($data);

    /**
     * write into session
     * @param $data
     * @return mixed
     */
    public function write($data);

    /**
     * get session
     * @return mixed
     */
    public function get();

    /**
     * delete session
     * @return mixed
     */
    public function destroy();

    /**
     * Cleanup old sessions
     * @return mixed
     */
    public function gc();
}