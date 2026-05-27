require("./setup");

describe("Real3D Flipbook Gutenberg Block", () => {
  let blockConfig;

  beforeAll(() => {
    global.r3dfb = [
      { id: "1", name: "Test Book" },
      { id: "2", name: "Another Book" },
    ];
    require("../../js/blocks.js");
    blockConfig = wp.blocks.registerBlockType.mock.calls[0][1];
  });

  test("registers the r3dfb/embed block", () => {
    expect(wp.blocks.registerBlockType).toHaveBeenCalledWith(
      "r3dfb/embed",
      expect.any(Object),
    );
  });

  test("block has expected attributes", () => {
    expect(blockConfig.attributes).toHaveProperty("id");
    expect(blockConfig.attributes.id.type).toBe("string");
    expect(blockConfig.attributes).toHaveProperty("pdf");
    expect(blockConfig.attributes.pdf.type).toBe("string");
    expect(blockConfig.attributes).toHaveProperty("mode");
    expect(blockConfig.attributes.mode.default).toBe("normal");
    expect(blockConfig.attributes).toHaveProperty("pages");
  });

  test("block has edit and save functions", () => {
    expect(typeof blockConfig.edit).toBe("function");
    expect(typeof blockConfig.save).toBe("function");
  });

  test("block title is Real3D FlipBook", () => {
    expect(blockConfig.title).toBe("Real3D FlipBook");
  });

  test("block category is media", () => {
    expect(blockConfig.category).toBe("media");
  });

  test("save function generates shortcode string", () => {
    const result = blockConfig.save({
      attributes: { id: "42", mode: "lightbox" },
    });

    expect(result).not.toBeNull();
  });
});
