const apiUrl = "http://localhost/filipino-cookbook-api-borja/public/api/foods";
const token = 'YOUR_API_TOKEN';

const recipesContainer = document.getElementById("foods");
const searchInput = document.getElementById("search");

let recipes = [];

// Images for each dish
const foodImages = {
    "Adobo": "images/adobo.jpg",
    "Chicken Inasal": "images/chicken-inasal.jpg",
    "Bicol Express": "images/bicol-express.jpg",
    "Laing": "images/laing.jpg",
    "Sinigang": "images/sinigang.jpg",
    "Kare-Kare": "images/kare-kare.jpg",
    "Bulalo": "images/bulalo.jpg",
    "Tinola": "images/tinola.jpg",
    "Pancit Canton": "images/pancit-canton.jpg",
    "Lumpiang Shanghai": "images/lumpiang-shanghai.jpg",
    "Pinakbet": "images/pinakbet.jpg",
    "Menudo": "images/menudo.jpg",
    "Afritada": "images/afritada.jpg",
    "Lechon Kawali": "images/lechon-kawali.jpg",
    "Halo-Halo": "images/halo-halo.jpg",

    // Default image
    "default": "images/default-food.jpg"
};

async function loadRecipes() {

    try {

        const response = await fetch(apiUrl, {
            headers: {
                "Authorization": "Bearer " + token
            }
        });

        if (!response.ok) {
            throw new Error("Failed to load recipes.");
        }

        recipes = await response.json();

        displayRecipes(recipes);

    } catch (error) {

        recipesContainer.innerHTML =
            "<h2 style='text-align:center;color:red;'>Unable to connect to the API.</h2>";

        console.error(error);

    }

}

function displayRecipes(data) {

    recipesContainer.innerHTML = "";

    if (data.length === 0) {

        recipesContainer.innerHTML =
            "<h2 style='text-align:center;'>No recipes found.</h2>";

        return;
    }

    data.forEach(recipe => {

        const image =
            foodImages[recipe.food_name] || foodImages["default"];

        const card = document.createElement("div");
        card.className = "card";

        card.innerHTML = `
            <img src="${image}" alt="${recipe.food_name}">

            <div class="card-content">

                <h2>${recipe.food_name}</h2>

                <p><strong>Category:</strong> ${recipe.category_name}</p>

                <p><strong>Origin:</strong> ${recipe.origin_name}</p>

                <h3>🥕 Ingredients</h3>

                <ul>
                    ${recipe.ingredients.map(item => `<li>${item}</li>`).join("")}
                </ul>

                <h3>👨‍🍳 Instructions</h3>

                <div class="instructions">
                    ${recipe.instructions}
                </div>

            </div>
        `;

        recipesContainer.appendChild(card);

    });

}

// Search Function
searchInput.addEventListener("keyup", function () {

    const keyword = this.value.toLowerCase();

    const filtered = recipes.filter(recipe =>
        recipe.food_name.toLowerCase().includes(keyword)
    );

    displayRecipes(filtered);

});

// Load recipes when page opens
loadRecipes();