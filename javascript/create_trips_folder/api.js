// src/api.js

export async function getFlightLocation(city, languagecode = 'en-us') {
    const url = `https://booking-com15.p.rapidapi.com/api/v1/flights/searchDestination?query=${encodeURIComponent(city)}&languagecode=${languagecode}`;
    const options = {
      method: 'GET',
      headers: {
        'x-rapidapi-key': 'a4f936d18fmsh72c62428617d573p1b6010jsn36884d1c931d',
        'x-rapidapi-host': 'booking-com15.p.rapidapi.com'
      }
    };
    try {
      const response = await fetch(url, options);
      if (!response.ok) {
        throw new Error(`Search destination request failed: ${response.status}`);
      }
      const result = await response.json();
      console.log('Search destination result for', city, result);
      if (result.data && result.data.length > 0) {
        // Choose the best match (this logic can be refined)
        const match =
          result.data.find(item =>
            item.name.toLowerCase().includes(city.toLowerCase())
          ) || result.data[0];
        return match.id;
      }
      return null;
    } catch (error) {
      console.error('Error in getFlightLocation:', error);
      return null;
    }
  }
  