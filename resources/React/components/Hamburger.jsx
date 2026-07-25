import React from 'react'
import { Link } from 'react-router-dom'

function Hamburger({ firstPage, secondPage }) {
    return (
        <div className="text-white">
            <h2><Link to={'/'}>{firstPage}</Link> <span>/ {secondPage}</span> </h2>
        </div>
    )
}

export default Hamburger
