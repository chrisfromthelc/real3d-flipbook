import js from "@eslint/js";
import globals from "globals";
import { defineConfig } from "eslint/config";

export default defineConfig([
  {
    ignores: [
      "**/node_modules/**",
      "**/vendor/**",
      "**/*.min.js",
      "**/*.min.css",
      "**/js/libs/**",
      "**/lib/**",
      "**/tests/**",
      "js/edit_flipbook_post.js",
      "js/flipbook.swipe.old.js",
    ],
  },
  {
    files: ["jest.config.js"],
    plugins: { js },
    extends: ["js/recommended"],
    languageOptions: {
      globals: {
        ...globals.node,
      },
    },
  },
  {
    files: ["**/*.{js,mjs,cjs}"],
    plugins: { js },
    extends: ["js/recommended"],
    languageOptions: {
      globals: {
        ...globals.browser,
        jQuery: "readonly",
        wp: "readonly",
        ajaxurl: "readonly",
        FLIPBOOK: "writable",
        FlipBook: "readonly",
        r3dfb: "readonly",
        r3d: "readonly",
        r3d_frontend: "readonly",
        r3d_stripslashes: "readonly",
        pdfjsLib: "readonly",
        Mark: "readonly",
        dataLayer: "writable",
        Swal: "readonly",
        YT: "readonly",
        THREE: "readonly",
        IScroll: "readonly",
        Color: "readonly",
        postboxes: "readonly",
        tb_remove: "readonly",
        flipbooks_json: "readonly",
        flipbooks: "readonly",
        flipbookOptions_global: "readonly",
        options: "readonly",
        json: "readonly",
        c: "readonly",
        wheelDeltaX: "writable",
        wheelDeltaY: "writable",
      },
    },
    rules: {
      "no-unused-vars": ["warn", { args: "none", caughtErrors: "none" }],
      "no-useless-assignment": "warn",
      "no-useless-escape": "warn",
      "no-self-assign": "warn",
      "no-case-declarations": "warn",
      "no-empty": ["error", { allowEmptyCatch: true }],
      "no-redeclare": "warn",
    },
  },
]);
