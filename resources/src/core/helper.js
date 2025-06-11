import Toastify from "toastify-js";
import axios from "axios";

window.axios = axios;
window.$heroicHelper = window.$heroicHelper || {};
const $heroicHelper = window.$heroicHelper;

global.$heroicHelper.cached = {};

// Fungsi untuk menyimpan cache
$heroicHelper.setCache = function (key, data) {
  $heroicHelper.cached[key] = data;
};

// Fungsi untuk mengambil cache
$heroicHelper.getCache = function (key) {
  return $heroicHelper.cached[key] ?? null;
};

// Fungsi untuk menghapus cache
$heroicHelper.clearCache = function (key) {
  delete $heroicHelper.cached[key];
};

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
$heroicHelper.fetch = function (url, headers = {}) {
  // Pastikan base_url diakhiri dengan '/'
  if (!base_url.endsWith("/")) {
    base_url += "/";
  }

  // Gabungkan base_url dan url
  let fullUrl = base_url + url;

  // Tentukan separator (kalau nanti mau ditambahkan query param)
  let separator = fullUrl.includes("?") ? "&" : "?";

  // Default headers
  const defaultHeaders = {
    "X-Requested-With": "XMLHttpRequest",
    Authorization: `Bearer ` + localStorage.getItem("heroic_token"),
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
      if (error.response && error.response.status === 401) {
        console.warn("Unauthorized. Removing token.");
        localStorage.removeItem("heroic_token");
      }
      console.error("Fetch error:", error);
      throw error; // Lempar ulang error supaya bisa ditangani caller
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

document.addEventListener("alpine:init", () => {
  Alpine.directive("debug", (el) => {
    const elId = el.id ? `#${el.id}` : `${el.tagName.toLowerCase()}[x-data]`;

    const wrapper = document.createElement("div");
    wrapper.style.position = "fixed";
    wrapper.style.bottom = "10px";
    wrapper.style.right = "10px";
    wrapper.style.zIndex = "100000";
    wrapper.style.maxHeight = "90vh";
    wrapper.style.overflowY = "auto";

    const toggleBtn = document.createElement("button");
    toggleBtn.innerText = `🐞 Debug ${elId}`;
    toggleBtn.style = `
            background: #333;
            color: white;
            font-size: 13px;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 5px;
            width: 300px;
            text-align: left;
        `;

    const panel = document.createElement("div");
    panel.style = `
            display: none;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 10px;
            background: #f9f9f9;
            font-size: 13px;
            font-family: monospace;
            max-height: 300px;
            overflow: auto;
            width: 300px;
        `;

    let interval = null;

    toggleBtn.addEventListener("click", () => {
      const isOpen = panel.style.display === "block";

      // Tutup semua panel debug lain (optional)
      document.querySelectorAll(".debug-panel").forEach((p) => {
        p.style.display = "none";
      });

      // Matikan semua interval aktif
      clearAllIntervals();

      if (isOpen) {
        panel.style.display = "none";
      } else {
        panel.style.display = "block";
        updatePanel();
        interval = setInterval(updatePanel, 1000);
        activeIntervals.push(interval);
      }
    });

    function updatePanel() {
      try {
        const data = Alpine.$data(el);
        panel.innerHTML = `<pre style="margin: 0;">${syntaxHighlight(
          data
        )}</pre>`;
      } catch (e) {
        panel.textContent = "Error loading data.";
      }
    }

    panel.classList.add("debug-panel");
    wrapper.appendChild(toggleBtn);
    wrapper.appendChild(panel);
    document.body.appendChild(wrapper);
  });

  // Utility: syntax highlighting untuk JSON
  function syntaxHighlight(json) {
    if (typeof json !== "string") {
      json = JSON.stringify(json, null, 2);
    }
    json = json
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;");

    return json.replace(
      /("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(?:\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g,
      function (match) {
        let cls = "number";
        if (/^"/.test(match)) {
          cls = /:$/.test(match) ? "key" : "string";
        } else if (/true|false/.test(match)) {
          cls = "boolean";
        } else if (/null/.test(match)) {
          cls = "null";
        }
        return `<span style="color: ${getColor(cls)}">${match}</span>`;
      }
    );
  }

  function getColor(cls) {
    switch (cls) {
      case "key":
        return "#d73a49";
      case "string":
        return "#032f62";
      case "number":
        return "#005cc5";
      case "boolean":
        return "#e36209";
      case "null":
        return "#6a737d";
      default:
        return "#000";
    }
  }

  // Untuk tracking dan membersihkan interval
  const activeIntervals = [];
  function clearAllIntervals() {
    activeIntervals.forEach((id) => clearInterval(id));
    activeIntervals.length = 0;
  }
});
