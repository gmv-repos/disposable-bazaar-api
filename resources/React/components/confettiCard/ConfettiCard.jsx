import { useEffect, useState, useRef } from "react";
import JSConfetti from "js-confetti";
import "./styles.css";

/*
 * Read the blog post here:
 * https://letsbuildui.dev/articles/building-a-react-confetti-component
 */
const ConfettiCard = ({ frontContent, backContent }) => {
  const [isFlipped, setIsFlipped] = useState(false);
  const canvasRef = useRef();
  const confettiRef = useRef();

  useEffect(() => {
    confettiRef.current = new JSConfetti({ canvas: canvasRef.current });
  }, []);

  const handleClick = () => {
    if (!isFlipped) {
      confettiRef.current.addConfetti({
        confettiRadius: 5,
        confettiNumber: 300
      });
    }

    setIsFlipped(!isFlipped);
  };

  return (
    <div className={`card ${isFlipped ? "active" : "inactive"}`}>
      <div className="card-front">
        <button type="button" onClick={handleClick} className="trigger icon">
          {frontContent}
        </button>
      </div>
      {/* <div className="card-back">
        {backContent} */}
        {/* <canvas className="canvas" ref={canvasRef} /> */}
        {/* <button type="button" className="reset" onClick={handleClick}>
          Replay
        </button> */}
      {/* </div> */}
    </div>
  );
};

export default ConfettiCard;
