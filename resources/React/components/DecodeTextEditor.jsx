import DOMPurify from "dompurify";
import parse from "html-react-parser";

// Component
const DecodeTextEditor = ({ body }) => {
  const options = {
    replace: (domNode) => {
      // Handle h1 tags
      if (domNode.name === "h1") {
        return (
          <h1 className="text-4xl font-bold my-4 leading-snug">
            {domNode.children?.map((child) => child.data || child.children?.[0]?.data)}
          </h1>
        );
      }

      // Handle h2 tags
      if (domNode.name === "h2") {
        return (
          <h2 className="text-3xl font-semibold my-3 leading-snug">
            {domNode.children?.map((child) => child.data || child.children?.[0]?.data)}
          </h2>
        );
      }

      // Handle h3 tags
      if (domNode.name === "h3") {
        return (
          <h3 className="text-2xl font-semibold my-3 leading-snug">
            {domNode.children?.map((child) => child.data || child.children?.[0]?.data)}
          </h3>
        );
      }

      // Handle h4 tags
      if (domNode.name === "h4") {
        return (
          <h4 className="text-xl font-semibold my-2">
            {domNode.children?.map((child) => child.data || child.children?.[0]?.data)}
          </h4>
        );
      }

      // Handle h5 tags
      if (domNode.name === "h5") {
        return (
          <h5 className="text-lg font-medium my-2">
            {domNode.children?.map((child) => child.data || child.children?.[0]?.data)}
          </h5>
        );
      }

      // Handle p tags (main body content)
      if (domNode.name === "p") {
        return (
          <p className="text-base leading-relaxed my-2">
            {domNode.children?.map((child) => child.data || child.children?.[0]?.data)}
          </p>
        );
      }

      // Handle span tags
      if (domNode.name === "span") {
        return (
          <span className="text-sm">
            {domNode.children?.map((child) => child.data || child.children?.[0]?.data)}
          </span>
        );
      }

      // Handle a tags (internal linking / external linking)
      if (domNode.name === "a") {
        return (
          <a
            href={domNode.attribs.href}
            className="text-blue-600 underline hover:text-blue-800 transition-colors"
            target={domNode.attribs.href?.startsWith("http") ? "_blank" : "_self"}
            rel="noopener noreferrer"
          >
            {domNode.children?.map((child) => child.data || child.children?.[0]?.data)}
          </a>
        );
      }

      // Handle img tags
      if (domNode.name === "img") {
        return (
          <img
            src={domNode.attribs.src}
            alt={domNode.attribs.alt || "Image"}
            className="my-4 w-full rounded-lg"
          />
        );
      }

      // Handle li tags
      if (domNode.name === "li") {
        return (
          <li className="list-disc ml-6 my-1 text-base leading-relaxed">
            {domNode.children?.map((child) => child.data || child.children?.[0]?.data)}
          </li>
        );
      }
    },
  };

  return (
    <div className="mb-4 font-poppins">
      {parse(DOMPurify.sanitize(body || ""), options)}
    </div>
  );
};

export default DecodeTextEditor;
