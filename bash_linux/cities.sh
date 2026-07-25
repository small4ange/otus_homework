

result=$(awk 'NR>1 {count[$3]++} END{for (city in count) print count[city], city}' users.txt | sort -rn | awk '{print $2}' | head -n 3)
