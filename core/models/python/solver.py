from lp_maker import *
import random
import math
import json
from pprint import pprint
import sys

# lp_maker(f,a,b,e, vlb, vub, xint, scalemode, setminim)
# f: n vector of coefficients for a linear objective function.
# a: m by n matrix representing linear constraints.
# b: m vector of right sides for the inequality constraints.
# e: m vector that determines the sense of the inequalities:
# e(i) = -1  ==> Less Than
# e(i) =  0  ==> Equals
# e(i) =  1  ==> Greater Than
# vlb: n vector of non-negative lower bounds. If empty or omitted, then the lower bounds are set to zero.
# vub: n vector of upper bounds. May be omitted or empty.
# xint: vector of integer variables. May be omitted or empty.
# scalemode: Autoscale flag. Off when 0 or omitted.
# setminim: Set maximum lp when this flag equals 0 or omitted.

def aVectorForWorkHours(workHours):
	if workHours == 12: 
		slot1  = [1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]
		slot2  = [1,1,1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]
		slot3  = [1,1,1,1,1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]
		slot4  = [1,1,1,1,1,1,1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]
		slot5  = [1,1,1,1,1,1,1,1,1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]
		slot6  = [1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0]
		slot7  = [1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,0,0,0,0,0,0,0,0,0]
		slot8  = [1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,0,0,0,0,0,0,0]
		slot9  = [1,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,0,0,0,0,0]
		slot10 = [1,0,1,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,0,0,0,0]
		slot11 = [1,0,1,0,1,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,0,0,0]
		slot12 = [1,0,1,0,1,0,1,0,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,0,0]
		slot13 = [1,0,1,0,1,0,1,0,1,0,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,0]
		slot14 = [1,0,1,0,1,0,1,0,1,0,1,0,1,1,1,1,1,1,1,1,1,1,1,0,0,0]
		slot15 = [1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,1,1,1,1,1,1,1,1,1,0,0]
		slot16 = [1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,1,1,1,1,1,1,1,1,0]
		slot17 = [0,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,1,1,1,1,1,1,1]
		slot18 = [0,0,0,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,0,1,1,1,1,1,1,1]
		slot19 = [0,0,0,0,0,0,1,0,1,0,1,0,1,0,1,0,1,0,0,0,1,1,1,1,1,1]
		slot20 = [0,0,0,0,0,0,0,0,1,0,1,0,1,0,1,0,1,0,0,0,0,1,1,1,1,1]
		slot21 = [0,0,0,0,0,0,0,0,0,0,1,0,1,0,1,0,1,0,0,0,0,0,1,1,1,1]
		slot22 = [0,0,0,0,0,0,0,0,0,0,0,0,1,0,1,0,1,0,0,0,0,0,0,1,1,1]
		slot23 = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,1,0,0,0,0,0,0,0,1,1]
		slot24 = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,1]
		fullAvailableConstraint = [1 , 0, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0]
		partAvailableConstraint = [0 , 1, 0, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1]
		A = [slot1, slot2, slot3, slot4, slot5, slot6, slot7, slot8, slot9, slot10, slot11, slot12, slot13, slot14, slot15, slot16, slot17, slot18, slot19, slot20, slot21, slot22, slot23, slot24, fullAvailableConstraint, partAvailableConstraint]

	if workHours == 10: 
		slot1  = [1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]
		slot2  = [1,1,1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0]
		slot3  = [1,1,1,1,1,1,0,0,0,0,0,0,0,0,0,0,0,0]
		slot4  = [1,1,1,1,1,1,1,1,0,0,0,0,0,0,0,0,0,0]
		slot5  = [1,1,1,1,1,1,1,1,1,1,0,0,0,0,0,0,0,0]
		slot6  = [1,1,1,1,1,1,1,1,1,1,1,0,0,0,0,0,0,0]
		slot7  = [1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,0,0,0]
		slot8  = [1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,0,0]
		slot9  = [1,0,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,0]
		slot10 = [1,0,1,0,1,1,1,1,1,1,1,1,1,1,1,0,0,0]
		slot11 = [1,0,1,0,1,0,1,1,1,1,1,1,1,1,1,1,0,0]
		slot12 = [1,0,1,0,1,0,1,0,1,1,1,1,1,1,1,1,1,0]
		slot13 = [1,0,1,0,1,0,1,0,1,0,1,1,1,1,1,1,1,1]
		slot14 = [1,0,1,0,1,0,1,0,1,0,0,1,1,1,1,1,1,1]
		slot15 = [1,0,1,0,1,0,1,0,1,0,0,0,1,1,1,1,1,1]
		slot16 = [1,0,1,0,1,0,1,0,1,0,0,0,0,1,1,1,1,1]
		slot17 = [0,0,1,0,1,0,1,0,1,0,0,0,0,0,1,1,1,1]
		slot18 = [0,0,0,0,1,0,1,0,1,0,0,0,0,0,0,1,1,1]
		slot19 = [0,0,0,0,0,0,1,0,1,0,0,0,0,0,0,0,1,1]
		slot20 = [0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,1]
		fullAvailableConstraint = [1,0,1,0,1,0,1,0,1,0,0,0,0,0,0,0,0,0]
		partAvailableConstraint = [0,1,0,1,0,1,0,1,0,1,1,1,1,1,1,1,1,1]
		A = [slot1, slot2, slot3, slot4, slot5, slot6, slot7, slot8, slot9, slot10, slot11, slot12, slot13, slot14, slot15, slot16, slot17, slot18, slot19, slot20, fullAvailableConstraint, partAvailableConstraint]

	if workHours == 8: 
		slot1  = [1,1,0,0,0,0,0,0,0,0]
		slot2  = [1,1,1,0,0,0,0,0,0,0]
		slot3  = [1,1,1,1,0,0,0,0,0,0]
		slot4  = [1,1,1,1,1,0,0,0,0,0]
		slot5  = [1,1,1,1,1,1,0,0,0,0]
		slot6  = [1,1,1,1,1,1,1,0,0,0]
		slot7  = [1,1,1,1,1,1,1,1,0,0]
		slot8  = [1,1,1,1,1,1,1,1,1,0]
		slot9  = [1,0,1,1,1,1,1,1,1,1]
		slot10 = [1,0,0,1,1,1,1,1,1,1]
		slot11 = [1,0,0,0,1,1,1,1,1,1]
		slot12 = [1,0,0,0,0,1,1,1,1,1]
		slot13 = [1,0,0,0,0,0,1,1,1,1]
		slot14 = [1,0,0,0,0,0,0,1,1,1]
		slot15 = [1,0,0,0,0,0,0,0,1,1]
		slot16 = [1,0,0,0,0,0,0,0,0,1]
		fullAvailableConstraint = [1,0,0,0,0,0,0,0,0,0]
		partAvailableConstraint = [0,1,1,1,1,1,1,1,1,1]
		A = [slot1, slot2, slot3, slot4, slot5, slot6, slot7, slot8, slot9, slot10, slot11, slot12, slot13, slot14, slot15, slot16, fullAvailableConstraint, partAvailableConstraint]

	return A

def fVectorForWorkHours(workHours, FULL, PART):
	if workHours == 12:
		f = [FULL , PART, FULL, PART, FULL, PART, FULL, PART, FULL, PART, FULL, PART, FULL, PART, FULL, PART, FULL, PART, PART, PART, PART, PART, PART, PART, PART, PART]
	if workHours == 10:
		f = [FULL, PART, FULL, PART, FULL, PART, FULL, PART, FULL, PART, PART, PART, PART, PART, PART, PART, PART, PART]
	if workHours == 8:
		f = [FULL, PART, PART, PART, PART, PART, PART, PART, PART, PART]
	return f


#Parse JSON settings from file
# json_settings = open('json_settings')
json_settings = open('zejsonfiles/settings.json')
settings_data = json.load(json_settings)
json_settings.close()

#Constants
FULL = 100
PART = 50
fullTimersAvailable = settings_data["fulltime_available_teachers"]
partTimersAvailable = settings_data["parttime_available_teachers"]
studentsPerTeacher = settings_data["max_students_per_teacher"]
workHours = settings_data["day_length"]

#Objective function
f = fVectorForWorkHours(workHours, FULL, PART)

#Create 'A' vector
A = aVectorForWorkHours(workHours)

#Create signs for equations
signs = [None] * len(A)
for x in range(0, len(A)):
	if x == (len(A) -1) or x == (len(A) -2):
		#full/part timers sign
		signs[x] = -1 	
	else:
		signs[x] = -1

#Parse JSON from file
# json_data = open('json_in')
json_data = open('zejsonfiles/students_schedule_count_for_python.json')
data = json.load(json_data)
json_data.close()

parsedDays = data["Days"]
numberOfDays = len(parsedDays)
daysArray = [None] * numberOfDays

#Iterate and solve for each Day
for day in range(0,numberOfDays):

	slots = parsedDays[day]["Slots"]

	#Create b vector from NumberOfStudents per Slot
	b = [None] * len(A)

	for slotNumber in range(0, len(A) - 2):
		# b[slotNumber] = math.floor(slots[slotNumber]["NumberOfStudents"] / studentsPerTeacher)
		b[slotNumber] = math.ceil(slots[slotNumber]["NumberOfStudents"] / studentsPerTeacher)
		
	b[len(A) -2] = fullTimersAvailable
	b[len(A) -1] = partTimersAvailable

	#Solve model
	lp = lp_maker(f, A, b, signs , None, None, None, 1, 0)
	solvestat = lpsolve('solve', lp)
	obj = lpsolve('get_objective', lp)
	x = lpsolve('get_variables', lp)[0]
	# print "x = " + str(x) 
	lpsolve('delete_lp', lp)


# Create full/part time array results			
	slotsArray = [None] * int(len(A)-2)

	for i in range(int(len(A)-2)):
		slot = A[i]
		fullCounter = 0 
		partCounter = 0
		for j in range(len(slot)):
			if slot[j] == 1:
				if f[j] == FULL:
					if x[j] < studentsPerTeacher:
						# fullCounter = math.floor(slots[j]["NumberOfStudents"] / studentsPerTeacher)
						# print "\n"
						# print "WRONG AT: " + str(j)
						# print "Number of students: " + str(slots[j]["NumberOfStudents"])
						# print "x[j] : " + str(x[j])
						# print "suggested fix: " + str(math.ceil( float(slots[j]["NumberOfStudents"]) / float(studentsPerTeacher) ))
						fullCounter += math.ceil( float(slots[j]["NumberOfStudents"]) / float(studentsPerTeacher) )
					else:
						fullCounter += x[j]	
				else:
					partCounter += x[j]

			slotsArray[i] = {"PartTime" : partCounter, "FullTime" : fullCounter}		

	daysArray[day] = {"Slots" : slotsArray}

# Create final output JSON struct
finalOutputDict = {"Days" : daysArray}

# Print finalOutputDict as JSON to console
# print "\n"
print json.dumps(finalOutputDict)

#Write final output to file
# with open('zejsonfiles/finalOut.json', 'w') as outfile:
  # json.dump(finalOutputDict, outfile)







