import React, { useEffect, useState } from 'react';
import { PiStarFill, PiStarThin } from 'react-icons/pi';
import { TbCameraPlus } from 'react-icons/tb';
import axios from '../../Utils/axios';
import { Rating } from './Rating';
import { Image_Url } from '../../const';
import { VscShare } from 'react-icons/vsc';
import { AiFillDislike, AiFillLike } from 'react-icons/ai';
import { split } from 'postcss/lib/list';
import { useUser } from '../../Context/UserContext';
import { Navigate, useNavigate } from 'react-router-dom';

function Review({ productId, setProductReview, productReview }) {
  const { user } = useUser();
  const [user_type, setUser_Type] = useState(2);
  const [Api_Type, setApi_Type] = useState('protected');
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [title, setTitle] = useState('');
  const [experience, setExperience] = useState('');
  const [ratingStars, setRatingStars] = useState(1);
  const [recommendation, setRecommendation] = useState(1);
  const [file, setFile] = useState(null);
  const [isReview, setIsReview] = useState([]);
  const [review, setReview] = useState(1);
  const [loading, setLoading] = useState(false);
  const navigate = useNavigate();

  console.log('Review My', productReview);

  // Handle file upload
  const handleFileChange = (e) => {
    setFile(e.target.files[0]);
  };

  console.log(user);
  const handleSubmit = async (e) => {
    e.preventDefault();
    console.log(productId);

    if (!user) {
      navigate('/login/')
    }

    const formData = new FormData();
    formData.append('user_type', user_type);
    formData.append('user_id', user.id);
    formData.append('name', name);
    formData.append('email', email);
    formData.append('rating', ratingStars);
    formData.append('title_of_review', title);
    formData.append('description', experience);
    formData.append('do_your_recomended_this_product', recommendation);
    formData.append('product_id', productId); // Adjust product ID based on your needs

    if (file) {
      formData.append('image', file);
    }
    try {
      setLoading(true);
      const response = await axios.protected.post('review_add', formData, {
        // const response = await axios[user ? 'protected' : 'public'].post('review_add', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });
      setName('');
      setEmail('');
      setRatingStars(1);
      setTitle('');
      setExperience("");
      setRecommendation('');
      document.getElementById('thumnail').value = '';
      console.log(response.data);
      setLoading(false)
      // handle success (e.g., show a success message)
    } catch (error) {
      setLoading(false)
      console.error('Error submitting the review:', error);
    }
  };
  useEffect(() => {
    if (productReview) {
      setIsReview(productReview.data)
      console.log('isReview', isReview);

    }
  }, [productReview])


  // Handle like action
  const handleLike = async (reviewId) => {
    console.log('Liking review:', reviewId);
    if (!user) {
      navigate('/login/')
    }
    try {
      const response = await axios.protected.post(`reviews/${reviewId}/like`, {
        is_like: 1,
      });

      if (response.status === 200) {
        setProductReview(prevReviews => ({
          ...prevReviews,
          data: prevReviews.data.map(review =>
            review.id === reviewId
              ? {
                ...review,
                likes_count: response.data.likes_count,
                dislikes_count: response.data.dislikes_count,
              }
              : review
          ),
        }));
      }
    } catch (error) {
      console.error('Error liking review:', error.response ? error.response.data : error.message);
    }
  };

  // Handle dislike action
  const handleDislike = async (reviewId) => {
    if (!user) {
      navigate('/login/')
    }
    console.log('Disliking review:', reviewId);
    try {
      const response = await axios.protected.post(`reviews/${reviewId}/like`, {
        is_like: 0,
      });

      if (response.status === 200) {
        setProductReview(prevReviews => ({
          ...prevReviews,
          data: prevReviews.data.map(review =>
            review.id === reviewId
              ? {
                ...review,
                dislikes_count: response.data.dislikes_count,
                likes_count: response.data.likes_count,
              }
              : review
          ),
        }));
      }
    } catch (error) {
      console.error('Error disliking review:', error.response ? error.response.data : error.message);
    }
  };


  return (
    <div className="">
      {Array.isArray(isReview) && isReview.length !== 0 ?
        (<Rating setProductReview={setProductReview} productReview={productReview} readOnly={true} />

        ) : (
          <h3 className='text-center text-4xl py-10 font-bazaar'>No Reviews Found</h3>
        )}

      <section>
        <div className="flex flex-row gap-4">
          {Array.isArray(isReview) && isReview.length !== 0 ? (
            <>
              <h3
                onClick={() => setReview(1)}
                className={`text-lg ${review === 1 ? 'border-b-2 border-[#1E7773] text-[#1E7773]' : 'text-[#90909c]'} font-semibold py-2`}
              >
                Write a Review
              </h3>
              <h3
                onClick={() => setReview(2)}
                className={`text-lg ${review === 2 ? 'border-b-2 border-[#1E7773] text-[#1E7773]' : 'text-[#90909c]'} font-semibold py-2`}
              >
                View Review
              </h3>
            </>
          ) : (
            <>
              <h3
                onClick={() => setReview(1)}
                className={`text-lg ${review === 1 ? 'border-b-2 border-[#1E7773] text-[#1E7773]' : 'text-[#90909c]'} font-semibold py-2`}
              >
                Write a Review
              </h3>
            </>
          )}


        </div>
        {review === 1 &&
          <form onSubmit={handleSubmit}>
            <div className="flex flex-col py-5 gap-3">
              <div className="md:w-1/2 flex flex-col md:flex-row gap-5">
                <div className="flex flex-col md:w-1/2">
                  <label className="text-[#90909c] text-md py-2" htmlFor="name">Name</label>
                  <input
                    type="text"
                    id="name"
                    className="border border-[#90909c] rounded bg-transparent p-1"
                    value={name}
                    onChange={(e) => setName(e.target.value)}
                  />
                </div>
                <div className="flex flex-col md:w-1/2">
                  <label className="text-[#90909c] text-md py-2" htmlFor="email">Email</label>
                  <input
                    type="email"
                    id="email"
                    className="border border-[#90909c] rounded bg-transparent p-1"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                  />
                </div>
              </div>
              <div className="">
                <label className="text-[#90909c] text-md py-2">Rating</label>
                <div className="flex flex-row gap-0.5 py-2">
                  {Array(5).fill(0).map((_, index) => (
                    index < ratingStars ? (
                      <PiStarFill
                        key={index}
                        className="text-2xl cursor-pointer transition-colors text-yellow-500 duration-200"
                        onClick={() => setRatingStars(index + 1)}
                      />
                    ) : (
                      <PiStarThin
                        key={index}
                        className="text-2xl cursor-pointer transition-colors duration-200"
                        onClick={() => setRatingStars(index + 1)}
                      />
                    )
                  ))}
                </div>
              </div>
              <div className="flex flex-col w-full">
                <label className="text-[#90909c] text-md py-2" htmlFor="Title">Title of Review</label>
                <input
                  type="text"
                  id="Title"
                  className="border border-[#90909c] rounded bg-transparent p-1"
                  value={title}
                  onChange={(e) => setTitle(e.target.value)}
                />
              </div>
              <div className="flex flex-col w-full">
                <label className="text-[#90909c] text-md py-2" htmlFor="experience">How was your overall experience?</label>
                <textarea
                  cols={4}
                  rows={6}
                  id="experience"
                  className="border border-[#90909c] rounded bg-transparent p-1"
                  value={experience}
                  onChange={(e) => setExperience(e.target.value)}
                ></textarea>
              </div>
              <div className="flex flex-row justify-between items-center">
                <div className="">
                  <p>Do you recommend this product?</p>
                  <div className="my-form flex flex-wrap gap-4 py-3 items-center">
                    <div className="flex flex-row items-center justify-center gap-2">
                      <input
                        onClick={() => setRecommendation(1)}
                        defaultChecked
                        id="yes"
                        type="radio"
                        name="option"
                      />
                      <label htmlFor="yes">Yes</label>
                    </div>
                    <div className="flex flex-row items-center justify-center gap-2">
                      <input
                        onClick={() => setRecommendation(2)}
                        id="no"
                        type="radio"
                        name="option"
                      />
                      <label htmlFor="no">No</label>
                    </div>
                  </div>
                </div>
                <div className="flex flex-wrap justify-center gap-1">
                  <div className="flex flex-col w-fit">
                    <div
                      id="upload"
                      className="flex flex-col relative w-fit h60 p-2 px-3 items-center justify-center border-2 border rounded-lg border-[#1E7773]"
                    >
                      <p className="font-bazaar flex flex-row gap-2 pt-1">
                        <TbCameraPlus size={22} />
                        ADD PHOTOS
                      </p>
                      <input
                        type="file"
                        id='thumnail'
                        name="thumbnail"
                        accept="image/*"
                        className="absolute inset-0 opacity-0 cursor-pointer"
                        onChange={handleFileChange}
                      />
                    </div>
                  </div>
                  <button
                    type="submit"
                    className="p-2 px-10 pt-3 bg-[#1E7773] w-fit lg:text-[15px] font-bazaar text-xs rounded-md"
                    disabled={loading}
                  >
                    {loading ? 'SUBMITING' : 'SUBMIT'}
                  </button>
                </div>
              </div>
            </div>
          </form>}
        {review === 2 &&
          <div className="border-b border-[#9F9F9F]">
            {isReview.map((review, index) => (
              <div key={index} className="py-10 border-b border-[#9F9F9F]">
                <div className="flex justify-start items-center">
                  <img className='w-16 h-16 rounded-full' src={`https://static.vecteezy.com/system/resources/previews/018/765/757/original/user-profile-icon-in-flat-style-member-avatar-illustration-on-isolated-background-human-permission-sign-business-concept-vector.jpg`} alt={`${review.name}`} />
                  <div className="ml-4 text-white">
                    <h2 className="text-2xl">{review.name}</h2>
                    <p className="text-md text-[#9F9F9F]">{review.created_at.slice(0, 10).split('-').join('/')}</p>
                  </div>
                </div>
                <div className="flex gap-1 pt-2">
                  {[...Array(review.rating)].map((_, i) => (
                    <PiStarFill key={i} className="text-yellow-500" size={'1.3rem'} />
                  ))}
                </div>
                <p className="text-xl text-white my-6">{review.description}</p>

                <div className="flex justify-between">
                  <div className="flex items-center gap-2 text-white text-xl">
                    <VscShare />
                    <h2>Share</h2>
                  </div>

                  <div className="flex items-center gap-2 text-white text-xl">
                    <h2 className='hidden md:block'>Was this helpful?</h2>
                    {/* <AiFillLike />
                    <span>{review.do_your_recomended_this_product}</span>
                    <AiFillDislike />
                    <span>{review.helpfulDislikes}</span> */}
                    <AiFillLike onClick={() => handleLike(review.id)} /> {/* Updated here */}
                    <span>{review.likes_count}</span>
                    <AiFillDislike onClick={() => handleDislike(review.id)} />
                    <span>{review.dislikes_count}</span>
                  </div>
                </div>
              </div>
            ))}
          </div>}
      </section>
    </div>
  );
}

export default Review;

// </div>
// </div>
// <div className="flex flex-col w-full">
//     <label className='text-[#90909c] text-md py-2' htmlFor="Title">Title of Review </label>
//     <input type="email" id='Title' className='border border-[#90909c] rounded bg-transparent p-1' />
// </div>
// <div className="flex flex-col w-full">
//     <label className='text-[#90909c] text-md py-2' htmlFor="experience">How was your overall experience ? </label>
//     <textarea name="" cols={4} rows={6} id='Title' className='border border-[#90909c] rounded bg-transparent p-1'></textarea>
// </div>
// <div className="flex md:flex-row flex-col justify-between items-center">
//     <div className="">
//         <p>Do yor recommend this product?</p>
//         <div className="my-form flex flex-wrap gap-4 py-3 items-center">
//             <div className="flex flex-row items-center justify-center gap-2">
//                 <input onClick={() => setRecommendation('yes')} defaultChecked id="yes" type="radio" name="option" />
//                 <label htmlFor="yes">Yes</label>
//             </div>
//             <div className="flex flex-row items-center justify-center gap-2">
//                 <input onClick={() => setRecommendation('no')} id="no" type="radio" name="option" />
//                 <label htmlFor="no">No</label>
//             </div>
//         </div>
//     </div>
//     <div className="flex flex-row justify-center gap-1">
//         {/* upload photos */}
//         <div className=" flex flex-col w-fit">
//             {/* <label htmlFor="upload">Upload Your Artwork If Any ( Logo/ Designs)</label> */}
//             <div
//                 id="upload"
//                 className="flex flex-col relative w-full h60 p-2 px-3 items-center justify-center border-2 border rounded-lg border-[#1E7773]"
//             >
//                 {/* <button className="px-3 py-2 bg-teal-700 text-white rounded-md">
//                     Select Files...
//                 </button> */}
//                 <p className='font-bazaar flex flex-row gap-2 pt-1'> <TbCameraPlus size={22} />ADD PHOTOS</p>
//                 <input
//                     type="file"
//                     name="thumbnail"
//                     accept="image/*"
//                     className="absolute inset-0 opacity-0 cursor-pointer"
//                 />
//             </div>
//         </div>
//         <button className='p-2 px-10 pt-3 bg-[#1E7773] w-fit lg:text-[15px] font-bazaar text-xs rounded-md'>SUBMIT</button>
