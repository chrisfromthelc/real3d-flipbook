// Mock WordPress globals for Jest tests
const jQueryMock = function () {
  return {
    appendTo() {
      return this;
    },
    attr() {
      return this;
    },
    val() {
      return "";
    },
    on() {
      return this;
    },
    find() {
      return this;
    },
    each() {
      return this;
    },
    text() {
      return this;
    },
    html() {
      return this;
    },
    css() {
      return this;
    },
    addClass() {
      return this;
    },
    removeClass() {
      return this;
    },
    hide() {
      return this;
    },
    show() {
      return this;
    },
  };
};
jQueryMock.fn = {};
jQueryMock.extend = function () {};

global.jQuery = jQueryMock;
global.$ = jQueryMock;

global.wp = {
  i18n: {
    __: (str) => str,
    _n: (s, p, n) => (n === 1 ? s : p),
    sprintf: (fmt) => fmt,
  },
  element: { createElement: function () {}, Fragment: "Fragment" },
  blocks: { registerBlockType: jest.fn() },
  blockEditor: {
    InspectorControls: function () {},
    MediaUpload: function () {},
    MediaUploadCheck: function () {},
  },
  components: {
    ServerSideRender: function () {},
    Button: function () {},
    TextControl: function () {},
    SelectControl: function () {},
    RadioControl: function () {},
    PanelBody: function () {},
    Placeholder: function () {},
    Disabled: function () {},
  },
};

global.ajaxurl = "/wp-admin/admin-ajax.php";
global.FLIPBOOK = {};
global.r3dfb = [];
global.r3d = { ajax_url: "/wp-admin/admin-ajax.php", nonce: "test_nonce" };
