/**
 * Client JSON monolithique KishalaTrans.
 * Utilise la session PHP (cookies) ; le Bearer JWT reste optionnel.
 */
(function (window) {
  'use strict';

  const BASE_URL = (window.KISHALA_BASE_URL || '/').replace(/\/?$/, '/');

  async function refreshSession() {
    try {
      const response = await fetch(BASE_URL + 'session/refresh', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { Accept: 'application/json' },
      });
      if (!response.ok) return false;
      const json = await response.json();
      if (json?.data?.token?.access_token) {
        window.KISHALA_API_TOKEN = json.data.token.access_token;
      }
      return true;
    } catch (e) {
      return false;
    }
  }

  async function apiFetch(url, options = {}, retry = true) {
    const headers = {
      Accept: 'application/json',
      ...(options.headers || {}),
    };

    const token = window.KISHALA_API_TOKEN || '';
    if (token) {
      headers.Authorization = 'Bearer ' + token;
    }

    let response = await fetch(url, {
      ...options,
      credentials: 'same-origin',
      headers,
    });

    if (response.status === 401 && retry) {
      const refreshed = await refreshSession();
      if (refreshed) {
        return apiFetch(url, options, false);
      }
      window.location.href = BASE_URL;
    }

    return response;
  }

  window.KishalaApi = { apiFetch, refreshSession, BASE_URL };
})(window);
