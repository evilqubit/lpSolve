## Description

Scheduler web app using PHP and Python.

## Usage

Drag/Drop students to the table then press Recalculate Schedule. The number of required teachers will be calculated and filled in each time slot.

## Contributors

Elie El Khoury | elie808.k at gmail<br>
Patrick Saad | patrick.saad.cs at gmail

## Docs

The algorithm is based on the bus driver schedule from this document:
http://ie501.cankaya.edu.tr/uploads/files/ie501model_10.pdf page 8
<br><br>Equation and example: <br>https://docs.google.com/spreadsheets/d/1WikKhowTrBZTN-bseI1XJd5_Vclc_JhguHfU_37YeYA/edit#gid=0

## JSON
Settings<br>
````
{
"day_starting_time": "07:00",
"day_ending_time": "17:00",
"day_length": 10,
"day_slot_time": "60",
"max_kids_per_teacher": 5,
"fulltime_available_teachers": 69,
"parttime_available_teachers": 69
}````
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