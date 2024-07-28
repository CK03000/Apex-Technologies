<?php include 'header.inc'; ?>

<section>
    <h1 id="apply-h1">Join Our Innovators: Start Your Apex Journey Today!</h1>
    <div class="container">
        <form action="processEOI.php" method="post" novalidate="novalidate">
            <fieldset>
                <legend>Candidate Information</legend>
                <div>
                    <label for="jobReferenceNumber">Job Reference No.</label>
                    <input
                        type="text"
                        name="JobReferenceNumber"
                        id="jobReferenceNumber"
                        minlength="5"
                        maxlength="5"
                        pattern="^[A-Za-z0-9]+$"
                        required
                    />
                </div>
                <div>
                    <label>First Name
                        <input
                            type="text"
                            name="FirstName"
                            maxlength="20"
                            pattern="^[a-zA-Z]+$"
                            required
                        />
                    </label>
                    <label>Last Name
                        <input
                            type="text"
                            name="LastName"
                            maxlength="20"
                            pattern="^[a-zA-Z]+$"
                            required
                        />
                    </label>
                </div>
                <div>
                    <label for="dob">Date of Birth</label>
                    <input type="date" name="DOB" id="dob" required />
                </div>
                <fieldset>
                    <legend>Gender</legend>
                    <input type="radio" name="Gender" id="male" value="Male" required />
                    <label for="male">Male</label>
                    <input type="radio" name="Gender" id="female" value="Female" />
                    <label for="female">Female</label>
                    <input type="radio" name="Gender" id="other" value="Other" />
                    <label for="other">Other</label>
                </fieldset>
            </fieldset>

            <fieldset>
                <legend>Candidate Residential Information</legend>
                <div>
                    <label for="streetAddress">Street Address</label>
                    <input
                        type="text"
                        name="StreetAddress"
                        id="streetAddress"
                        maxlength="40"
                        required
                    />
                </div>
                <div>
                    <label for="suburbTown">Suburb/Town</label>
                    <input
                        type="text"
                        name="SuburbTown"
                        id="suburbTown"
                        maxlength="40"
                        required
                    />
                </div>
                <div>
                    <label for="state">State</label>
                    <select name="State" id="state" required>
                        <option value="">Select State</option>
                        <option value="VIC">VIC</option>
                        <option value="NSW">NSW</option>
                        <option value="QLD">QLD</option>
                        <option value="NT">NT</option>
                        <option value="WA">WA</option>
                        <option value="SA">SA</option>
                        <option value="TAS">TAS</option>
                        <option value="ACT">ACT</option>
                    </select>
                </div>
                <div>
                    <label for="postcode">Postcode
                        <input
                            type="text"
                            name="PostCode"
                            id="postcode"
                            pattern="^[0-9]{4}$"
                            maxlength="4"
                            required
                        />
                    </label>
                </div>
            </fieldset>

            <fieldset>
                <legend>Candidate Contacts</legend>
                <div>
                    <label for="emailAddress">Email Address
                        <input
                            type="email"
                            name="EmailAddress"
                            id="emailAddress"
                            placeholder="example@email.com"
                            required
                        />
                    </label>
                </div>
                <div>
                    <label for="phoneNumber">Phone Number
                        <input
                            type="tel"
                            name="PhoneNumber"
                            id="phoneNumber"
                            placeholder="(##) ####-####"
                            required
                        />
                    </label>
                </div>
            </fieldset>

            <fieldset>
                <legend>Candidate Skills</legend>
                <div>
                    <label for="skill1">Programming</label>
                    <input type="checkbox" name="Skills[]" id="skill1" value="Programming" checked="checked" />
                    <label for="skill2">Design</label>
                    <input type="checkbox" name="Skills[]" id="skill2" value="Design" />
                    <label for="skill3">Communication/Help Desk</label>
                    <input type="checkbox" name="Skills[]" id="skill3" value="Communication" />
                    <label for="skill4">Management</label>
                    <input type="checkbox" name="Skills[]" id="skill4" value="Management" />
                    <label for="otherSkillsCheckbox">Other Skills</label>
                    <input type="checkbox" name="Skills[]" id="otherSkillsCheckbox" value="OtherSkills">
                    <br>
                    <textarea
                        name="OtherSkills"
                        id="otherSkills"
                        cols="35"
                        rows="3"
                        placeholder="What other skills do you have?"
                    ></textarea>
                </div>
            </fieldset>

            <button type="submit">Apply</button>
        </form>
    </div>
</section>

<?php include 'footer.inc'; ?>
