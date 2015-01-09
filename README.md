## Scheduler App
-----------------
### Installation
Run the following command on ubuntu :
```
apt-get install python-dev gcc gfortran python-setuptools python-pip python-numpy python-numpy-dev python-scipy libglpk-dev python-cvxopt lp-solve liblpsolve55-dev
```
To install lpsolve55 follow this [link](https://racingtadpole.com/blog/install-lpsolve-for-python/)

### Description

Scheduler web app using PHP and Python.

### Usage

Drag/Drop students to the table then press Recalculate Schedule. The number of required teachers will be calculated and filled in each time slot.

### Contributors

Elie El Khoury | elie808.k at gmail<br>
Patrick Saad | patrick.saad.cs at gmail

## Docs

The algorithm is based on the bus driver schedule from this document:
http://ie501.cankaya.edu.tr/uploads/files/ie501model_10.pdf page 8
<br><br>Equation and example: <br>https://docs.google.com/spreadsheets/d/1WikKhowTrBZTN-bseI1XJd5_Vclc_JhguHfU_37YeYA/edit#gid=0

## JSON
### Constraints :

- every student has a unique id which is used on the schedule when drag/dropping the student into slots (to be able to disallow placing the same student in the same slot), and when initially loading the schedule with students already saved into it. There's no way we don't use an id to reference students.
- if a schedule was computed, and then the Settings were changed after that, it perhaps the slot total count has changed, therefore the previously computed schedule is now invalid (different slot count), thus the code does not accept this and simply resets the schedule to an empty one with the correct number of slots for the current Settings.
- if the json file had invalid format (someone manually changed it or for whatever reasons), we also reset the schedule to empty.

### How to import :
So, for importing the schedule as JSON file, you will have to follow these guidelines or else the file will be reset in these cases:
1- if the total slot count is wrong (i.e: the settings are a 10 hour day while the JSON import file has 8 slots per day)
2- if the total day count is wrong (i.e the JSON import file doesn't have 5 total days)

If you add student entries to the JSON import file which do not exist in our Students JSON file (saved when adding entries in the Students page), these entries will be deleted from your JSON file (file won't be reset if the reset conditions were not met).

All student entries in the JSON file must have a valid id and name, both which are shown under the Students page. The name can be optional although I am currently checking for its validity. The reason I suggest for it to remain mandatory is for you to have a visual reference when creating your JSON file. Let me know if you only want to only enter the student id without its name in the import file so I fix a couple of lines in the code.

Schedule JSON format:
```
{"Days":[{"Slots":[{"Students":{"0":{"id":2,"name":"Simon"},"1":{"id":1,"name":"Patrick"},"3":{"id":3,"name":"Joy"}}},{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]}]},{"Slots":[{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]}]},{"Slots":[{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]}]},{"Slots":[{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]}]},{"Slots":[{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]},{"Students":[]}]}]}
```
The example JSON above is a schedule for a 10 hour day with 3 students in the first slot on the first day. You can use http://jsonlint.com to better view the hieararchy. I hope you are familiar with JSON to be able to create the schedule that you want.

That's it for the Import Schedule part, it's tricky, but that's to be expected in such a project. You will be the import functionality under Settings.


---------------------------
### Settings:<br>
````
{
"day_starting_time": "07:00",
"day_ending_time": "17:00",
"day_length": 10,
"day_slot_time": "60",
"max_kids_per_teacher": 5,
"fulltime_available_teachers": 69,
"parttime_available_teachers": 69
}
````
<br>
<br>
Student Schedule used to draw the table<br>
```
{
    "Days": [
        {
            "Slots": [
                {
                    "Students": [
                        {
                            "id": 1,
                            "name": "John"
                        }
                    ]
                }
            ]
        }
    ]
}
```
<br><br>
Student Schedule Count sent to Python<br>
```
{
    "Days": [
        {
            "Slots": [
                {
                    "NumberOfStudents": 15
                }
            ]
        }
    ]
}
```
<br><br>
Teachers Schedule received from Python<br>
```
{
    "Days": [
        {
            "Slots": [
                {
                    "FullTime": 0,
                    "PartTime": 0
                }
            ]
        }
    ]
}
```
