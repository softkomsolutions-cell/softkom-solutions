<?php
/**
 * Softkom Funnel V2 security and lead-risk engine.
 *
 * Provides:
 * - Honeypot detection
 * - Minimum completion-time checks
 * - Rate limiting
 * - Suspicious-input checks
 * - Email/domain sanity checks
 * - Spam/fraud risk scoring
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * Normalize risk score.
 */
function softkom_v3_security_clamp_score( $score ) {
    return max( 0, min( 100, (int) round( $score ) ) );
}


/**
 * Return client IP address.
 *
 * Uses REMOTE_ADDR only to avoid trusting spoofable forwarding headers.
 */
function softkom_v3_security_client_ip() {

    return isset( $_SERVER['REMOTE_ADDR'] )
        ? sanitize_text_field(
            wp_unslash( $_SERVER['REMOTE_ADDR'] )
        )
        : '';
}


/**
 * Simple disposable / suspicious domain list.
 *
 * Keep this list intentionally small and conservative.
 */
function softkom_v3_security_blocked_email_domains() {

    return array(
        'mailinator.com',
        'guerrillamail.com',
        '10minutemail.com',
        'tempmail.com',
        'temp-mail.org',
        'yopmail.com',
        'sharklasers.com',
        'trashmail.com',
        'dispostable.com',
    );
}


/**
 * Extract email domain.
 */
function softkom_v3_security_email_domain( $email ) {

    $email = sanitize_email( $email );

    if ( ! is_email( $email ) ) {
        return '';
    }

    $parts = explode( '@', strtolower( $email ) );

    return isset( $parts[1] )
        ? sanitize_text_field( $parts[1] )
        : '';
}


/**
 * Basic suspicious text test.
 */
function softkom_v3_security_suspicious_text( $value ) {

    $value = strtolower(
        trim( wp_strip_all_tags( (string) $value ) )
    );

    if ( '' === $value ) {
        return false;
    }

    $patterns = array(
        '<script',
        'javascript:',
        'data:text/html',
        'viagra',
        'casino',
        'crypto giveaway',
        'investment guaranteed',
        'click here now',
        'http://',
        'https://',
    );

    foreach ( $patterns as $pattern ) {

        if ( false !== strpos( $value, $pattern ) ) {
            return true;
        }
    }

    return false;
}


/**
 * Detect low-quality nonsense-style values.
 */
function softkom_v3_security_low_quality_text( $value ) {

    $value = trim(
        wp_strip_all_tags( (string) $value )
    );

    if ( strlen( $value ) < 2 ) {
        return true;
    }

    /*
     * Excessive repeated characters:
     * aaaaaaa, xxxxxxx, 1111111, etc.
     */
    if ( preg_match( '/(.)\1{5,}/', $value ) ) {
        return true;
    }

    /*
     * Very low alphabetic content.
     */
    if (
        strlen( $value ) >= 5 &&
        ! preg_match( '/[a-zA-Z]{2,}/', $value )
    ) {
        return true;
    }

    return false;
}


/**
 * Rate-limit repeated assessment submissions.
 *
 * Default: maximum 5 submissions from one IP in 15 minutes.
 */
function softkom_v3_security_rate_limit_check() {

    $ip = softkom_v3_security_client_ip();

    if ( '' === $ip ) {
        return array(
            'limited' => false,
            'count'   => 0,
        );
    }

    $key = 'softkom_assess_rate_' . md5( $ip );

    $count = (int) get_transient( $key );

    $count++;

    set_transient(
        $key,
        $count,
        15 * MINUTE_IN_SECONDS
    );

    return array(
        'limited' => $count > 5,
        'count'   => $count,
    );
}


/**
 * Classify spam/fraud risk.
 */
function softkom_v3_security_risk_level( $score ) {

    $score = softkom_v3_security_clamp_score( $score );

    if ( $score >= 80 ) {
        return 'BLOCK';
    }

    if ( $score >= 55 ) {
        return 'HIGH RISK';
    }

    if ( $score >= 30 ) {
        return 'REVIEW';
    }

    return 'LOW RISK';
}


/**
 * Evaluate assessment submission risk.
 *
 * Expected payload:
 *
 * first_name
 * last_name
 * email
 * company
 * honeypot
 * started_at
 * completed_at
 */
function softkom_v3_security_evaluate_submission( $payload ) {

    $score   = 0;
    $reasons = array();

    $first_name = isset( $payload['first_name'] )
        ? sanitize_text_field( $payload['first_name'] )
        : '';

    $last_name = isset( $payload['last_name'] )
        ? sanitize_text_field( $payload['last_name'] )
        : '';

    $company = isset( $payload['company'] )
        ? sanitize_text_field( $payload['company'] )
        : '';

    $email = isset( $payload['email'] )
        ? sanitize_email( $payload['email'] )
        : '';

    $honeypot = isset( $payload['honeypot'] )
        ? trim( (string) $payload['honeypot'] )
        : '';

    $started_at = isset( $payload['started_at'] )
        ? absint( $payload['started_at'] )
        : 0;

    $completed_at = isset( $payload['completed_at'] )
        ? absint( $payload['completed_at'] )
        : time();


    /*
     * Honeypot.
     */
    if ( '' !== $honeypot ) {

        $score += 100;
        $reasons[] = 'honeypot_triggered';
    }


    /*
     * Completion time.
     *
     * A genuine 14-question assessment + qualification form
     * is unlikely to be completed in under 20 seconds.
     */
    if ( $started_at > 0 && $completed_at >= $started_at ) {

        $elapsed = $completed_at - $started_at;

        if ( $elapsed < 10 ) {

            $score += 50;
            $reasons[] = 'completion_too_fast';

        } elseif ( $elapsed < 20 ) {

            $score += 25;
            $reasons[] = 'completion_unusually_fast';
        }
    }


    /*
     * Email checks.
     */
    $domain = softkom_v3_security_email_domain(
        $email
    );

    if ( '' === $domain ) {

        $score += 40;
        $reasons[] = 'invalid_email_domain';

    } elseif (
        in_array(
            $domain,
            softkom_v3_security_blocked_email_domains(),
            true
        )
    ) {

        $score += 60;
        $reasons[] = 'disposable_email_domain';
    }


    /*
     * Suspicious input.
     */
    foreach (
        array(
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'company'    => $company,
            'email'      => $email,
        )
        as $field => $value
    ) {

        if (
            softkom_v3_security_suspicious_text(
                $value
            )
        ) {

            $score += 30;
            $reasons[] =
                'suspicious_' . $field;
        }
    }


    /*
     * Low-quality text.
     */
    foreach (
        array(
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'company'    => $company,
        )
        as $field => $value
    ) {

        if (
            softkom_v3_security_low_quality_text(
                $value
            )
        ) {

            $score += 15;
            $reasons[] =
                'low_quality_' . $field;
        }
    }


    /*
     * Rate limiting.
     */
    $rate = softkom_v3_security_rate_limit_check();

    if ( $rate['limited'] ) {

        $score += 70;
        $reasons[] = 'rate_limit_exceeded';
    }


    $score = softkom_v3_security_clamp_score(
        $score
    );

    return array(
        'risk_score'  => $score,
        'risk_level'  => softkom_v3_security_risk_level(
            $score
        ),
        'risk_reasons'=> array_values(
            array_unique( $reasons )
        ),
        'rate_count'  => isset( $rate['count'] )
            ? (int) $rate['count']
            : 0,
    );
}
