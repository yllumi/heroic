import Toastify from "toastify-js";
import axios from "axios";

window.axios = axios;
window.$heroicHelper = window.$heroicHelper || {};
const $heroicHelper = window.$heroicHelper;

global.$heroicHelper.cached = {};

// Contoh fungsi
$heroicHelper.toastr = function (message, type = "success", position = "top") {
  Toastify({
    text: message,
    close: true,
    duration: 5000,
    className: type,
    gravity: position,
    offset: { y: 40 },
  }).showToast();
};

/**************************************************************************
 * Fetch Ajax Data
 **************************************************************************/
$heroicHelper.fetch = function (page, headers = {}) {
  // Pastikan base_url diakhiri dengan '/'
  if (!base_url.endsWith("/")) {
    base_url += "/";
  }

  // Gabungkan base_url dan page
  let fullUrl = base_url + page;

  // Tentukan separator (kalau nanti mau ditambahkan query param)
  let separator = fullUrl.includes("?") ? "&" : "?";

  // Default headers
  const defaultHeaders = {
    "X-Requested-With": "XMLHttpRequest",
  };

  // Merge defaultHeaders dengan headers dari parameter
  const finalHeaders = {
    ...defaultHeaders,
    ...headers,
  };

  return axios
    .get(fullUrl, {
      headers: finalHeaders,
    })
    .then((response) => {
      return response;
    })
    .catch((error) => {
      console.error("Fetch error:", error);
    });
};

/**************************************************************************
 * Post Ajax Data
 **************************************************************************/
$heroicHelper.post = function (url, data = {}, headers = {}) {
  // Membuat objek FormData
  const formData = new FormData();

  for (const key in data) {
    if (data.hasOwnProperty(key)) {
      const value = data[key];

      if (Array.isArray(value)) {
        value.forEach((item) => formData.append(`${key}[]`, item));
      } else if (value instanceof File || value instanceof Blob) {
        formData.append(key, value);
      } else if (typeof value === "object" && value !== null) {
        formData.append(key, JSON.stringify(value));
      } else {
        formData.append(key, value);
      }
    }
  }

  // Header default
  const defaultHeaders = {
    "X-Requested-With": "XMLHttpRequest",
    Authorization: `Bearer ` + localStorage.getItem("heroic_token"),
  };

  // Merge defaultHeaders dan custom headers
  const finalHeaders = {
    ...defaultHeaders,
    ...headers,
  };

  return axios
    .post(url, formData, {
      headers: finalHeaders,
    })
    .then((response) => {
      return response;
    })
    .catch((error) => {
      console.error("Post error:", error);
    });
};

/**************************************************************************
 * Helper Functions
 **************************************************************************/

// COOKIE SETTER GETTER
$heroicHelper.setCookie = function (name, value, days) {
  let expires = "";
  if (days) {
    let date = new Date();
    date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
    expires = "; expires=" + date.toUTCString();
  }
  document.cookie = name + "=" + (value || "") + expires + "; path=/";
};

$heroicHelper.getCookie = function (name) {
  let nameEQ = name + "=";
  let ca = document.cookie.split(";");
  for (let i = 0; i < ca.length; i++) {
    let c = ca[i];
    while (c.charAt(0) == " ") c = c.substring(1, c.length);
    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
  }
  return null;
};

$heroicHelper.nl2br = function (str, is_xhtml) {
  if (typeof str === "undefined" || str === null) {
    return "";
  }
  var breakTag =
    is_xhtml || typeof is_xhtml === "undefined" ? "<br />" : "<br>";
  return (str + "").replace(
    /([^>\r\n]?)(\r\n|\n\r|\r|\n)/g,
    "$1" + breakTag + "$2"
  );
};

$heroicHelper.formatDate = function (dateString) {
  if (dateString && dateString != "0000-00-00") {
    const date = new Date(dateString);
    const options = { day: "numeric", month: "long", year: "numeric" };
    return new Intl.DateTimeFormat("id-ID", options).format(date);
  }
  return "";
};

$heroicHelper.currency = function (amount) {
  return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
};
