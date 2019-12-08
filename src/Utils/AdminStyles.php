<?php

namespace EvolveEngine\Utils;

class AdminStyles
{
    // Make sure only loaded max once
    protected static $loadedCustomACFStyle = false;

    /**
     * Remove unused metaboxes
     *
     * @return void
     */
    public function removeMetaBoxes()
    {
        // Hide metaboxes
        remove_meta_box('revisionsdiv', 'page', 'normal');
        remove_meta_box('commentstatusdiv', 'page', 'normal');
        remove_meta_box('sendgrid_statistics_widget', 'dashboard', 'normal');
    }

    /**
     * Generates footer link to admin screen (wordpress backend)
     *
     * @return void
     */
    public function trueFooter()
    {
        ?>
            <style>
                #wpfooter a {
                    outline: none !important; text-decoration: none !important;
                } 
                .wp-footer-true-link {
                    position: relative;
                    display: block; color: #aeaeae;
                    -webkit-transition: color 0.22s ease;
                    -o-transition: color 0.22s ease;
                    transition: color 0.22s ease;
                }
                .wp-footer-true-link:before {
                    content: '';
                    position: absolute;
                    bottom: 0; margin-left: 50%;
                    width: 0%; height: 1px;
                    background-color: #333;
                    -webkit-transition: width 0.28s cubic-bezier(0.63, 0.62, 0.48, 1.3),
                                margin-left 0.28s cubic-bezier(0.63, 0.62, 0.48, 1.3);
                    -o-transition: width 0.28s cubic-bezier(0.63, 0.62, 0.48, 1.3),
                                margin-left 0.28s cubic-bezier(0.63, 0.62, 0.48, 1.3);
                    transition: width 0.28s cubic-bezier(0.63, 0.62, 0.48, 1.3),
                                margin-left 0.28s cubic-bezier(0.63, 0.62, 0.48, 1.3);
                }
                .wp-footer-true-link > img {
                    width: 45px; height: auto; position: relative; top: 2px;
                    opacity: 0.7;
                    -webkit-transition: opacity 0.22s ease;
                    -o-transition: opacity 0.22s ease;
                    transition: opacity 0.22s ease;
                }
                .wp-footer-true-link:hover {
                    color: #333;
                }
                .wp-footer-true-link:hover:before {
                    width: 100%; margin-left: 0%;
                }
                .wp-footer-true-link:hover > img {
                    opacity: 1;
                }
            </style>
            <a href="https://www.trueagency.com.au" target="_blank" class="wp-footer-true-link">
                True Agency
            </a>
        <?php
    }

    /**
     * Show recommended size in prettier way
     *
     * Usage: [widthxheight]
     * E.g.: [300x500]
     *
     */
    public function renderSize($field)
    {
        preg_match_all("/\[(.*?)\]/", $field['instructions'], $matches);

        if (count($matches) > 1) {
            if (count($matches[1]) > 0) {
                $match = $matches[1][0];
                $orig = '[' . $match . ']';

                $sizeArr = explode('x', $match);
                if (count($sizeArr) !== 2) {
                    return $field;
                }
                $width = $sizeArr[0];
                $height = $sizeArr[1];

                $render = '<span class="acf-recommended-size"> <span>' . $width . 'px</span><span>' . $height . 'px</span></span>';
                $field['instructions'] = str_replace($orig, $render, $field['instructions']);
            }
        }

        return $field;
    }

    /**
     * Custom admin styling for login
     *
     * @return void
     */
    public function loginStyles()
    {
        ?>
        <style type="text/css">
            body.login {
                position: relative;
                background-color: #f6f9fc;
                background-position: bottom center;
                background-repeat: no-repeat;
            }
            .login h1 a {
                filter: grayscale(1);
                opacity: 0.1;
            }
            .login #login {
                width: 340px;
            }
            .login form#loginform {
                background-color: transparent;
                box-shadow: none;
                padding-bottom: 0;
            }
            .login form#loginform label[for="user_login"],
            .login form#loginform label[for="user_pass"] {
                font-size: 11px;
                text-transform: uppercase;
            }
            .login form#loginform input[type="text"],
            .login form#loginform input[type="password"] {
                padding: 15px 15px;
                height: auto;
                border: 0;
                border-radius: 3px;
                background-color: #fff;
                box-shadow: 0 1px 3px 0 #cfd7df;
                font-size: 1.1rem;
            }
            .login form#loginform input[type="text"]::placeholder,
            .login form#loginform input[type="password"]::placeholder {
                color: #ddd;
            }
            .login form#loginform input:-webkit-autofill,
            .login form#loginform input:-webkit-autofill:hover, 
            .login form#loginform input:-webkit-autofill:focus, 
            .login form#loginform input:-webkit-autofill:active  {
                background-color: none;
            }
            .login form#loginform .forgetmenot {
                float: none;
            }
            .login form#loginform .submit {
                margin-top: 15px;
            }
            .login form#loginform .button-primary {
                float: none;
                width: 100%;
                padding: 5px 30px;
                box-shadow: none;
                border: 0;
                height: 40px;
                font-size: 0.9rem;
                text-transform: uppercase;
                text-shadow: none;
            }
        </style>
        <script>
            window.addEventListener('DOMContentLoaded', function (event) {
                document.getElementById("user_login")
                    .setAttribute("placeholder", "Username");
                document.getElementById("user_pass")
                    .setAttribute("placeholder", "Password");
            });
        </script>
        <?php
    }

    /**
     * Custom ACF style modifications
     *
     * @return void
     */
    public function customACFStyle()
    {
        if (defined('DOING_AJAX') && DOING_AJAX) {
            return;
        }

        if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
            return;
        }

        if (!self::$loadedCustomACFStyle) {
            ?>
            <style>
            .acf-field.field_type-message {
                padding: 15px 0px 0px !important;
                width: 100%;
            }
            .acf-field.field_type-message .acf-label {
                margin-bottom: 0px; display: inline-block; padding: 5px 10px; background-color: rgba(59, 150, 170, 1); color: #fff;
            }
            .acf-field.field_type-message .acf-input h4,
            .acf-field.field_type-message .acf-input h5,
            .acf-field.field_type-message .acf-input p
             {
                margin-top: 0px; margin-bottom: 0px;
                padding: 10px 15px; background-color: #2DA7AD; color: #fff;
            }
            .acf-image-uploader {
                padding-top: 50px;
            }
            span.acf-recommended-size {
                position: absolute; 
                top: 0; right: 0;
                display: inline-block; z-index: 10;
                padding: 0px 0px 0px 0px;
                padding-bottom: 0px;
                background-color: #fff;
                border-radius: 6px;
                border: 1px solid #ddd;
                overflow: hidden;
                /* box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23); */
            }

            /* span.acf-recommended-size:before {
                position: absolute; content: '';
                width: 24px; height: 24px;
                background-image: url("<?= asset('images/common/icon-size.png') ?>");
                background-position: center center; background-repeat: no-repeat;
                background-size: 20px 20px;
                top: 0px; left: 0px;
                padding-left: 6px;
                background-color: #393939;
            } */

            /* @media only screen and (-webkit-min-device-pixel-ratio: 2),
            only screen and (   min--moz-device-pixel-ratio: 2),
            only screen and (     -o-min-device-pixel-ratio: 2/1),
            only screen and (        min-device-pixel-ratio: 2),
            only screen and (                min-resolution: 192dpi),
            only screen and (                min-resolution: 2dppx) {
                span.acf-recommended-size:before {
                    background-image: url("<?= asset('img/common/icon-size.png') ?>");
                    background-size: 25px 25px;
                    -webkit-background-size: 25px 25px;
                    border-top-left-radius: 6px;
                    border-bottom-left-radius: 6px;
                }
            } */
            span.acf-recommended-size > span {
                line-height: 24px; height: 24px; text-align: center;
                padding-left: 6px;
                padding-right: 6px;
                color: #666;
                display: inline-block; position: relative;
            }

            /* span.acf-recommended-size > span:before {
                position: absolute; content: 'Width';
                line-height: 24px; height: 24px; width: 50px; text-align: center;
                bottom: -24px; left: 0px; width: 50px;
                background-color: #1abc9c; opacity: 0;
                -webkit-transition: opacity 0.2s ease 0s; -o-transition: opacity 0.2s ease 0s; transition: opacity 0.2s ease 0s;
            } */

            /* span.acf-recommended-size > span + span {
                background-color: #52ADEA;
            } */

            /* span.acf-recommended-size > span + span:before {
                background-color: #37BCA1; content: 'Height';
            }

            span.acf-recommended-size:hover {
                padding-bottom: 24px;
            }

            span.acf-recommended-size:hover:before {
                padding-bottom: 24px;
            }
            span.acf-recommended-size:hover > span:before {
                opacity: 1;
                -webkit-transition: opacity 0.2s ease 0.08s; -o-transition: opacity 0.2s ease 0.08s; transition: opacity 0.2s ease 0.08s;
            } */
            .acf-image-uploader.has-value {
                padding-top: 0px;
            }
            .acf-field.acf-field-image.acf-banner-image .acf-image-uploader .hide-if-value {
                margin-top: 50px;
            }
            .acf-field.acf-field-image.acf-banner-image .acf-image-uploader.has-value {
                min-height: 100px;
            }
            .acf-field.acf-field-image.acf-banner-image .show-if-value {
                position: absolute;
                z-index: 1000;
                height: auto;
                max-height: 100px;
                max-width: 100% !important;
                overflow: hidden;
                cursor: pointer;
                box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
                -webkit-transition: height .22s ease, max-height .22s ease, box-shadow .22s ease;
                -o-transition: height .22s ease, max-height .22s ease, box-shadow .22s ease;
                transition: height .22s ease, max-height .22s ease, box-shadow .22s ease
            }
            .acf-field.acf-field-image.acf-banner-image .show-if-value img {
                -webkit-transform: translate(0, -30px);
                -ms-transform: translate(0, -30px);
                -o-transform: translate(0, -30px);
                transform: translate(0, -30px);
                -webkit-transition: transform .22s ease;
                -o-transition: transform .22s ease;
                transition: transform .22s ease
            }
            .acf-field.acf-field-image.acf-banner-image .show-if-value:before {
                content: 'Click to expand';
                position: absolute;
                left: 50%;
                top: 10px;
                background-color: rgba(0, 0, 0, 0.5);
                color: #fff;
                font-size: 11px;
                z-index: 10;
                padding: 1px 10px;
                transform: translateX(-50%);
                border-radius: 10px;
                display: none;
            }
            .acf-field.acf-field-image.acf-banner-image .show-if-value:hover:before {
                display: block;
            }
            .acf-field.acf-field-image.acf-banner-image .show-if-value:after {
                content: '';
                position: absolute;
                height: 20px;
                width: 100%;
                bottom: 0;
                left: 0;
                background-image: -webkit-linear-gradient(top, transparent 0, rgba(0, 0, 0, .25) 100%);
                background-image: -o-linear-gradient(top, transparent 0, rgba(0, 0, 0, .25) 100%);
                background-image: linear-gradient(to bottom, transparent 0, rgba(0, 0, 0, .25) 100%);
                background-repeat: repeat-x;
                opacity: 1;
                filter: alpha(opacity=100);
                -webkit-transition: opacity .01s linear .22s;
                -o-transition: opacity .01s linear .22s;
                transition: opacity .01s linear .22s
            }
            .acf-field.acf-field-image.acf-banner-image .show-if-value.shown {
                max-height: 470px;
                box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
                z-index: 1001;
            }
            .acf-field.acf-field-image.acf-banner-image .show-if-value.shown:before {
                display: none;
            }
            .acf-field.acf-field-image.acf-banner-image .show-if-value.shown img {
                -webkit-transform: translate(0, 0);
                -ms-transform: translate(0, 0);
                -o-transform: translate(0, 0);
                transform: translate(0, 0)
            }
            .acf-field.acf-field-image.acf-banner-image .show-if-value.shown:after {
                opacity: 0;
                filter: alpha(opacity=0);
                -webkit-transition: opacity .01s;
                -o-transition: opacity .01s;
                transition: opacity .01s
            }
            </style>
            <script>
            (function () {
                window.addEventListener('DOMContentLoaded', () => {
                    jQuery('.acf-banner-image .show-if-value').on('click', function () {
                        jQuery(this).toggleClass('shown');
                    });
                });
            })();
            </script>
        <?php
        }
        self::$loadedCustomACFStyle = true;
    }

    /**
     * Add more dashboard widgets
     */
    public function addDashWidgets()
    {
        wp_add_dashboard_widget('custom_help_widget_2017', 'True Agency', [$this, 'brandDashboard']);
    }

    /**
     * Brand dashboard to our liking
     *
     * @return void
     */
    public function brandDashboard()
    {
        $cachePath = base_path('announcement.json');
        $feedUrl = config('theme.announcement');
        if (!$feedUrl) {
            // Default url
            $feedUrl = 'https://trueagency.com.au/dash-announcement.php';
        }
        $announcement = new DashboardAnnouncement(
            $cachePath,
            $feedUrl
        );
        $output = $announcement->getAnnouncement();
        echo $output;
    }
}
