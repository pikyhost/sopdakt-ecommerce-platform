<?php

if (!function_exists('displayAlert')) {
    function displayAlert()
    {

        if (session()->has('success')) {
            return '<script>
                Swal.fire({
                    icon: "success",
                    title: "success",
                    text: "' . session()->get('success') . '",
                     //hide the "OK" button
                    showConfirmButton: false,
                    //hide after 5 seconds
                    timer: 5000
                });
            </script>';
        } elseif (session()->has('error')) {
            return '<script>
                Swal.fire({
                    icon: "error",
                    title:  "error",
                    text: "' . session()->get('error') . '",
                      //hide the "OK" button
                    showConfirmButton: false,
                    //hide after 5 seconds
                    timer: 5000
                });
            </script>';
        } elseif (session()->has('warning')) {
            return '<script>
                Swal.fire({
                    icon: "warning",
                    title:  "warning",
                    text: "' . session()->get('warning') . '",
                    //hide the "OK" button
                    showConfirmButton: false,
                    //hide after 5 seconds
                    timer: 5000


                });
            </script>';
        } elseif (session()->has('info')) {
            return '<script>
                Swal.fire({
                    icon: "info",
                    title: "info",
                    text: "' . session()->get('info') . '",
                     //hide the "OK" button
                    showConfirmButton: false,
                    //hide after 5 seconds
                    timer: 5000
                });
            </script>';
        }
    }
}
