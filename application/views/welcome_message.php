<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />

  <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/img/apple-touch-icon.png'); ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/img/favicon-32x32.png'); ?>">
  <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/img/favicon-16x16.png'); ?>">
  <!-- <link rel="icon" href="<?= base_url('assets/img/apja-icon.png'); ?>" type="image/png"> -->
  <link rel="manifest" href="<?= base_url('assets/img/site.webmanifest'); ?>">

  <title>PT. Artha Pratama Jaya Abadi</title>
  <style>
    :root{
      --bg: #ffffff;
      --primary: #15358f; /* warna biru logo, bisa disesuaikan */
      --text: #16326b;
      --btn-bg: transparent;
      --btn-border: rgba(22,50,107,0.12);
      --btn-hover-bg: rgba(22,50,107,0.06);
      --max-logo-width: 720px;
    }

    /* Reset sederhana */
    *{box-sizing:border-box;margin:0;padding:0}
    html,body{height:100%}
    body{
      font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      background: var(--bg);
      color: var(--text);
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
    }

    /* Layout: full-screen center */
    .stage{
      min-height:100vh;
      display:flex;
      align-items:center;
      justify-content:center;
      padding:40px 20px;
      position:relative;
      text-align:center;
    }

    /* Login button top-right */
    .login-btn{
      position:absolute;
      top:20px;
      right:20px;
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding:10px 16px;
      border-radius:8px;
      border:1px solid var(--btn-border);
      background:var(--btn-bg);
      color:var(--text);
      text-decoration:none;
      font-weight:600;
      font-size:14px;
      transition:all .18s ease;
      backdrop-filter: blur(4px);
    }
    .login-btn:focus{
      outline:3px solid rgba(21,53,143,0.12);
      outline-offset:2px;
    }
    .login-btn:hover{
      transform:translateY(-2px);
      background:var(--btn-hover-bg);
      box-shadow: 0 6px 18px rgba(21,53,143,0.06);
    }

    /* Content center block */
    .center-block{
      max-width:1200px;
      width:100%;
      display:flex;
      flex-direction:column;
      align-items:center;
      gap:22px;
    }

    /* Logo */
    .logo-wrap{
      width:100%;
      display:flex;
      align-items:center;
      justify-content:center;
    }
    .logo{
      width: min(66vw, var(--max-logo-width));
      max-height: 48vh;
      object-fit: contain;
      display:block;
      filter: none;
      -webkit-filter: none;
    }

    /* Company name under logo */
    .company-name{
      font-size: clamp(14px, 2vw, 24px);
      letter-spacing: 1px;
      font-weight:700;
      color:var(--primary);
    }

    /* Small caption (opsional) */
    .subtitle{
      font-size:14px;
      color: #5b6b95;
      opacity:0.9;
    }

    /* Responsive tweaks */
    @media (max-width:520px){
      .login-btn{ top:12px; right:12px; padding:8px 12px; font-size:13px }
      .center-block{ gap:14px }
      .logo{ width: min(78vw, 420px); max-height:38vh }
    }

    @media (min-width:1600px){
      /* buat logo lebih besar di layar sangat lebar */
      :root{ --max-logo-width: 1100px; }
    }
  </style>
</head>
<body>
  <div class="stage">
    <a class="login-btn" href="<?= base_url('login'); ?>" aria-label="Login ke sistem">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
        <path d="M10 17L15 12L10 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M15 12H3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M21 19V5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      LOGIN
    </a>

    <div class="center-block" role="main">
      <div class="logo-wrap" aria-hidden="false">
        <img class="logo" src="<?= base_url('assets/img/apja-logo.png'); ?>" alt="Logo PT. Artha Pratama Jaya Abadi" />
      </div>

      <div class="company-name">WEBSITE DALAM TAHAP PENGEMBANGAN</div>
      <div class="subtitle">FRESH • HALAL • SEHAT</div>
    </div>
  </div>
</body>
</html>
