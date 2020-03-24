@servers(['server_37' => 'hcmdev@13.56.116.37'])
 
@task('deploy', ['on' => 'server_37'])
    ls -la
@endtask